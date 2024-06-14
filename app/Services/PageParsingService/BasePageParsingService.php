<?php

declare(strict_types=1);

namespace App\Services\PageParsingService;

use App\Data\Model\PageModelData;
use App\Data\ParserPageData;
use App\Exceptions\ParserException;
use App\Models\Page;
use App\Models\Resource;
use App\Queries\PageQuery;
use App\Services\PageService;
use App\Services\UrlService;
use Arr;
use Carbon\Carbon;
use Config;
use Exception;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class BasePageParsingService implements IPageParsingService
{
    private const array TAGS_TO_EXCLUDE = [
        'head',
        'header',
        'footer',
        'nav',
        'aside',
        'script',
        'img',
        'iframe',
        'noscript',
        'link',
        'h1',
        'button',
        'dialog',
    ];

    protected const array CLASSES_TO_EXCLUDE = [];

    public function __construct(
        private readonly UrlService $urlService,
        private readonly PageService $pageService,
        private readonly PageQuery $pageQuery,
    ) {
    }

    public function serve(string $urlKey, Crawler $crawler, Resource $resource): void
    {
        $externalId = $this->urlService->getExternalId($resource->id, $urlKey);
        $page = Page::whereExternalId($externalId)->first();

        if (!empty($page) && $page->is_active === false) {
            return;
        }

        [$title, $content, $metaTitle, $metaDescription, $contentId] = $this->getContent($crawler);

        $parserPageData = ParserPageData::from([
            'url' => sprintf('%s%s', $resource->url, $urlKey),
            'externalId' => $externalId,
            'resourceId' => $resource->id,
            'parsedAt' => Carbon::now(),
            'title' => $title,
            'content' => $content,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'contentId' => $contentId,
            'changedAt' => empty($page) || $contentId !== $page->content_id ? Carbon::now() : $page->changed_at,
        ]);

        try {
            $this->checkUrl($urlKey);
            $this->checkUrlParameters($urlKey);
            $this->checkContent($content);
            $this->checkForm($crawler);
            $this->checkDuplicateContent($page, $parserPageData);
            $this->check($urlKey);
        } catch (Exception|Throwable $e) {
            if ($e instanceof ParserException && !empty($page) && $page->is_active) {
                $this->pageService->delete($page);
            }

            return;
        }

        $pageModelData = PageModelData::from([
            'resourceId' => $parserPageData->resourceId,
            'url' => $parserPageData->url,
            'isActive' => !empty($page) ? $page->is_active : true,
            'externalId' => $parserPageData->externalId,
            'metaTitle' => $parserPageData->metaTitle,
            'metaDescription' => $parserPageData->metaDescription,
            'title' => $parserPageData->title,
            'content' => $parserPageData->content,
            'synonyms' => !empty($page) ? $page->synonyms : null,
            'parsedAt' => $parserPageData->parsedAt,
            'changedAt' => $parserPageData->changedAt,
            'contentId' => $parserPageData->contentId,
        ]);

        $this->update($pageModelData, $page);
    }

    protected function check(string $urlKey): void
    {
    }

    /**
     * @return array<int, string|null>cl
     */
    private function getContent(Crawler $crawler): array
    {
        $excludes = array_merge(self::TAGS_TO_EXCLUDE, static::CLASSES_TO_EXCLUDE);
        $excludes = array_unique($excludes);
        $excludes = implode(', ', $excludes);

        $title = $crawler->filter('h1')->count() > 0
            ? $crawler->filter('h1')->text()
            : null;

        $metaTitle = $crawler->filter('title')->count() > 0
            ? $crawler->filter('title')->text()
            : null;

        $metaDescription = $crawler->filter('meta[name="description"]')->count() > 0
            ? $crawler->filter('meta[name="description"]')->attr('content')
            : null;

        $crawler->each(function ($node) use ($excludes) {
            $node->filter($excludes)->each(function ($innerNode) {
                $innerNode->getNode(0)->parentNode->removeChild($innerNode->getNode(0));
            });
        });

        if ($crawler->filter('body main')->count() > 0) {
            $html = $crawler->filter('body main')->html();
        } else {
            $html = $crawler->filter('body')->count() > 0
                ? $crawler->filter('body')->html()
                : '';
        }

        $content = strip_tags($html);
        /** @var string $content */
        $content = preg_replace('/\n+/', ' ', $content);
        /** @var string $content */
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);

        $contentId = implode('-', [$title, $content, $metaTitle, $metaDescription]);
        $contentId = md5($contentId);

        return [$title, $content, $metaTitle, $metaDescription, $contentId];
    }

    /**
     * @throws ParserException
     */
    private function checkUrl(string $urlKey): void
    {
        $extensions = ['html', 'php'];
        $urlInfo = pathinfo($urlKey);

        $extension = Arr::get($urlInfo, 'extension');

        if (!empty($extension) && in_array($extension, $extensions)) {
            throw new ParserException(sprintf('Url contains %s', implode(', ', $extensions)));
        }
    }

    /**
     * @throws ParserException
     */
    private function checkUrlParameters(string $urlKey): void
    {
        $clearUrlKey = $this->urlService->getClearUrl($urlKey);

        if ($urlKey !== $clearUrlKey) {
            throw new ParserException('Url contains GET parameters');
        }
    }

    /**
     * @throws ParserException
     */
    private function checkContent(?string $content): void
    {
        /** @var int $minPageContentSize */
        $minPageContentSize = Config::get('app.parser_min_page_content_size');

        if (empty($content) || strlen($content) < $minPageContentSize) {
            throw new ParserException('Insufficient content');
        }
    }

    /**
     * @throws ParserException
     */
    private function checkDuplicateContent(?Page $page, ParserPageData $parserPageData): void
    {
        $isDuplicatePage = $this->pageQuery->isDuplicatePageContent($page, $parserPageData->contentId);

        if ($isDuplicatePage) {
            throw new ParserException('This content already exists');
        }
    }

    /**
     * @throws ParserException
     */
    private function checkForm(Crawler $crawler): void
    {
        if ($crawler->filter('form')->count() > 0) {
            throw new ParserException('The page contains a form');
        }
    }

    private function update(PageModelData $pageModelData, ?Page $page = null): void
    {
        if (empty($page)) {
            $this->pageService->create($pageModelData);
        } else {
            $this->pageService->update($pageModelData, $page);
        }
    }
}
