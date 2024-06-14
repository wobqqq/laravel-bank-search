<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ResourceNames;
use App\Exceptions\ParserException;
use App\Models\Resource;
use App\Queries\PageQuery;
use App\Services\PageParsingService\PageParsingService;
use Arr;
use Config;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Log;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response as HttpStatus;

final class ResourceParsingService
{
    private const array DOUBLE_REQUESTS = [
        ResourceNames::INTREB_BANCATRANSILVANIA_RO->value,
    ];

    private readonly GuzzleClient $guzzleClient;

    /** @var array<string, string> */
    private array $processedUrls = [];

    /** @var array<string, string> */
    private array $urlQueue = [];

    /** @var array<string, string> */
    private array $urlErrors = [];

    /** @var array<string, string> */
    private array $notFoundUrls = [];

    private int $parserQueueProcesses;

    public function __construct(
        private readonly UrlService $urlService,
        private readonly PageParsingService $pageParsingService,
        private readonly PageQuery $pageQuery,
        private readonly Resource $resource,
        private readonly PageService $pageService,
    ) {
        /** @var string $requestRetries */
        $requestRetries = Config::get('app.parser_request_retries');
        /** @var string $parserQueueProcesses */
        $parserQueueProcesses = Config::get('app.parser_queue_processes');
        /** @var string $parserTimeout */
        $parserTimeout = Config::get('app.parser_timeout');

        $stack = HandlerStack::create();
        $cookieJar = new CookieJar();
        $stack->push(
            Middleware::retry(
                function (int $retries, Request $request, Response $response = null, Exception $e = null) use (
                    $requestRetries
                ) {
                    if (
                        $retries < $requestRetries
                        && ($e instanceof ServerException || $e instanceof ConnectException)
                    ) {
                        return true;
                    }

                    return false;
                }
            )
        );
        $this->guzzleClient = new GuzzleClient([
            'verify' => false,
            'http_errors' => false,
            'timeout' => $parserTimeout,
            'handler' => $stack,
            'cookies' => $cookieJar,
        ]);
        $this->parserQueueProcesses = (int)$parserQueueProcesses;
    }

    /**
     * @throws ParserException
     */
    public function serveResource(): void
    {
        $baseUrls = $this->getBaseUrls();

        $this->addUrlToQueue($baseUrls);

        $requestUrls = $this->getUrlsFromQueue();

        $rootPageUrl = $this->urlService->getUrlFormat($this->resource->url);

        while (!empty($requestUrls)) {
            try {
                $responses = $this->sendRequests($requestUrls);

                if (in_array($this->resource->name, self::DOUBLE_REQUESTS)) {
                    $responses = $this->sendRequests($requestUrls);
                }

                foreach ($responses as $urlKey => $response) {
                    /** @var string $pageUrl */
                    $pageUrl = Arr::get($requestUrls, $urlKey, '');
                    $this->servePage($urlKey, $pageUrl, $rootPageUrl, $response, $requestUrls);
                }
            } catch (Exception|\Throwable $e) {
                $this->printLogError($e, ['request_urls' => $requestUrls]);
            }

            $requestUrls = $this->getUrlsFromQueue();
        }

        $this->deleteNotFoundPages();

        Log::info(
            sprintf(
                "%s pages of the resource %s were scanned \n",
                count($this->processedUrls),
                $this->resource->name
            )
        );
    }

    /**
     * @param array<string, string> $requestUrls
     * @throws ParserException
     * @throws \Throwable
     */
    private function servePage(
        string $urlKey,
        string $pageUrl,
        string $rootPageUrl,
        Response $response,
        array $requestUrls
    ): void {
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html, $rootPageUrl);

        $pageUrls = $this->scanUrls($crawler);
        $pageUrls = array_diff_key($pageUrls, $requestUrls);

        $this->addUrlToQueue($pageUrls);
        $this->addToProcessedUrls($urlKey, $pageUrl);

        $this->serveSpecificPage($pageUrl, $crawler);

        try {
            $this->pageParsingService->service($this->resource)->serve($urlKey, clone $crawler, $this->resource);
        } catch (Exception|\Throwable $e) {
            $this->printLogError($e, ['url_key' => $urlKey]);
        }

        $this->printPageResultToConsole($pageUrl);
    }

    /**
     * @param array<string, string> $requestUrls
     * @return array<string, Response>
     * @throws \Throwable
     */
    private function sendRequests(array $requestUrls): array
    {
        /** @var Promise[] $promises */
        $promises = array_map(function ($url) {
            return $this->guzzleClient->getAsync($url);
        }, $requestUrls);

        /** @var array<string, array<string, string|Response>> $responses */
        $responses = Utils::settle($promises)->wait();

        $result = [];

        foreach ($responses as $urlKey => $response) {
            $state = Arr::get($response, 'state');
            /** @var Response $value */
            $value = Arr::get($response, 'value');
            $urls = [$urlKey => $urlKey];

            if ($state !== 'fulfilled') {
                $this->addUrlToErrors($urls);
                continue;
            }

            if ($value->getStatusCode() === HttpStatus::HTTP_OK) {
                $result[$urlKey] = $response;
            } elseif ($value->getStatusCode() === HttpStatus::HTTP_NOT_FOUND) {
                $this->addUrlToNotFound($urls);
            } else {
                $this->addUrlToErrors($urls);
            }
        }

        $result = array_map(function (array $response) {
            /** @var Response $value */
            $value = Arr::get($response, 'value');

            return $value;
        }, $result);

        return $result;
    }

    private function serveSpecificPage(string $pageUrl, Crawler $crawler): void
    {
        switch ($this->resource->name) {
            case ResourceNames::BANCATRANSILVANIA_RO->value:
            case ResourceNames::BLOG_BANCATRANSILVANIA_RO->value:
                if ($crawler->filter('#nextPageBtn')->count() > 0) {
                    $this->serveOctoberCmsAjaxPagination($pageUrl, 1, 'Posts::onLoadMore');
                }
                break;
            default:
        }
    }

    private function serveOctoberCmsAjaxPagination(string $url, int $page, string $handler): void
    {
        try {
            $responses = $this->sendOctoberCmsAjaxPaginationRequests($url, $page, $handler);

            if (in_array($this->resource->name, self::DOUBLE_REQUESTS)) {
                $responses = $this->sendOctoberCmsAjaxPaginationRequests($url, $page, $handler);
            }

            if (empty($responses)) {
                return;
            }

            $page += count($responses);

            $isNextPageRequest = true;

            foreach ($responses as $response) {
                try {
                    $content = $response->getBody()->getContents();
                    /** @var array<string, string> $partials */
                    $partials = empty($content) ? [] : json_decode($content, true);
                    $partials = array_filter($partials);
                } catch (Exception|\Throwable $e) {
                    $partials = [];
                }

                if (empty($partials)) {
                    $isNextPageRequest = false;

                    continue;
                }

                $html = implode('', $partials);
                $crawler = new Crawler($html, $this->resource->url);
                $urls = $this->scanUrls($crawler);

                $this->addUrlToQueue($urls);
            }

            if ($isNextPageRequest) {
                $this->serveOctoberCmsAjaxPagination($url, $page, $handler);
            }
        } catch (Exception|\Throwable $e) {
            $this->printLogError($e, ['request_url' => $url]);
        }
    }

    /**
     * @return Response[]
     * @throws \Throwable
     */
    private function sendOctoberCmsAjaxPaginationRequests(string $url, int $page, string $handler): array
    {
        /** @var Promise[] $promises */
        $promises = [];

        while (count($promises) < $this->parserQueueProcesses) {
            ++$page;

            echo sprintf("sendOctoberCmsAjaxPaginationRequests Url: %s Page: %s\n", $url, $page);

            $promises[$page] = $this->guzzleClient->postAsync($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-October-Request-Handler' => $handler,
                    'X-Requested-With' => 'XMLHttpRequest',
                ],
                'body' => json_encode(['nextPage' => $page]),
            ]);
        }

        /** @var array<string, array<string, string|Response>> $responses */
        $responses = Utils::settle($promises)->wait();
        /** @var array<string, null|Response> $responses */
        $responses = array_map(function (array $response) {
            $state = Arr::get($response, 'state');

            return $state === 'fulfilled' ? Arr::get($response, 'value') : null;
        }, $responses);
        $responses = array_filter($responses);
        $responses = array_filter($responses, function (Response $response) {
            return $response->getStatusCode() === HttpStatus::HTTP_OK;
        });

        return $responses;
    }

    /**
     * @param array<string, string> $urls
     */
    private function addUrlToQueue(array $urls): void
    {
        $urls = array_diff_key(
            $urls,
            $this->processedUrls,
            $this->urlQueue,
            $this->urlErrors,
            $this->notFoundUrls,
        );

        foreach ($urls as $urlKey => $url) {
            $clearUrlKey = $this->urlService->getClearUrl($urlKey);
            $clearUrl = $this->urlService->getClearUrl($url);

            $this->urlQueue[$urlKey] = $url;
            $this->urlQueue[$clearUrlKey] = $clearUrl;
        }
    }

    /**
     * @return array<string, string>
     */
    private function getUrlsFromQueue(): array
    {
        return array_splice($this->urlQueue, 0, $this->parserQueueProcesses);
    }

    /**
     * @param array<string, string> $urls
     */
    private function addUrlToErrors(array $urls): void
    {
        foreach ($urls as $urlKey => $url) {
            $this->urlErrors[$urlKey] = $url;
        }
    }

    /**
     * @param array<string, string> $urls
     */
    private function addUrlToNotFound(array $urls): void
    {
        foreach ($urls as $urlKey => $url) {
            $this->notFoundUrls[$urlKey] = $url;
        }
    }

    private function addToProcessedUrls(string $urlKey, string $pageUrl): void
    {
        $this->processedUrls[$urlKey] = $pageUrl;
    }

    /**
     * @return array<string, string>
     * @throws ParserException
     */
    private function scanUrls(Crawler $crawler): array
    {
        $urls = $crawler->filter('a')->each(function (Crawler $node) {
            return $node->link()->getUri();
        });
        $urls = $this->getUrls($urls);

        return $urls;
    }

    /**
     * @return array<int, string>
     */
    private function getUrlsFromSitemap(): array
    {
        if (empty($this->resource->sitemap_url)) {
            return [];
        }

        try {
            $response = $this->guzzleClient->get($this->resource->sitemap_url);
            $crawler = new Crawler($response->getBody()->getContents(), $this->resource->sitemap_url);

            $sitemapUrls = $crawler->filterXPath('//*[local-name()="loc"]')->each(function (Crawler $node) {
                return $node->text();
            });
        } catch (Exception|\Throwable $e) {
            $sitemapUrls = [];
        }

        return $sitemapUrls;
    }

    /**
     * @return array<string, string>
     * @throws ParserException
     */
    private function getBaseUrls(): array
    {
        $dbUrls = $this->pageQuery->getPageUrlsByResource($this->resource);
        $sitemapUrls = $this->getUrlsFromSitemap();

        $urls = array_merge($sitemapUrls, $dbUrls, [$this->resource->url]);
        $urls = $this->getUrls($urls);

        return $urls;
    }

    /**
     * @param string[] $urls
     * @return array<string, string>
     * @throws ParserException
     */
    private function getUrls(array $urls): array
    {
        $urls = array_filter($urls);
        $urls = array_unique($urls);

        $result = [];

        foreach ($urls as $url) {
            $urlData = $this->urlService->getPageUrlData($this->resource->url, $url);

            if (empty($urlData)) {
                continue;
            }

            [$urlKey, $url] = $urlData;

            $result[$urlKey] = $url;
        }

        return $result;
    }

    private function deleteNotFoundPages(): void
    {
        $externalIds = array_map(function (string $urlKey) {
            return $this->urlService->getExternalId($this->resource->id, $urlKey);
        }, $this->notFoundUrls);

        $this->pageService->deletePagesByExternalIds($externalIds);
    }

    private function printPageResultToConsole(string $pageUrl): void
    {
        echo sprintf("Page \"%s\" processed\n", $pageUrl);
        echo sprintf("Processed %s URLs\n", count($this->processedUrls));
        echo sprintf("%s URLs in queue\n", count($this->urlQueue));
        echo sprintf("%s not found URLs\n", count($this->notFoundUrls));
        echo sprintf("%s URL errors\n", count($this->urlErrors));
        echo "---------------------------------\n";
    }

    /**
     * @param Exception|\Throwable $e
     * @param array<string, mixed> $data
     */
    private function printLogError(Exception|\Throwable $e, array $data = []): void
    {
        $defaultData = [
            'title' => sprintf('%s resource error', $this->resource->name),
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString(),
        ];

        $data = array_merge($defaultData, $data);

        Log::error(print_r($data, true));
    }
}
