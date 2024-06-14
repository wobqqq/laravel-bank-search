<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ParserException;
use Arr;

final class UrlService
{
    /**
     * @return array<int,string>|null
     * @throws ParserException
     */
    public function getPageUrlData(string $rootPageUrl, string $pageUrl): ?array
    {
        $isNotUrl = !$this->isUrl($pageUrl);

        if ($isNotUrl) {
            return null;
        }

        $rootPageUrl = $this->getUrlFormat($rootPageUrl);
        $pageUrl = $this->getUrlFormat($pageUrl, $rootPageUrl);

        $isFile = !($rootPageUrl === $pageUrl) && $this->isFile($pageUrl);
        $isExternalUrl = $this->isExternalUrl($rootPageUrl, $pageUrl);

        if ($isExternalUrl || $isFile) {
            return null;
        }


        if ($rootPageUrl === $pageUrl) {
            $key = $this->getUrlKey($rootPageUrl);

            return [$key, $rootPageUrl];
        }

        $key = $this->getUrlKey($pageUrl);

        return [$key, $pageUrl];
    }

    public function getExternalId(int $resourceId, string $key): string
    {
        $externalId = sprintf('%s-%s', $resourceId, $key);
        $externalId = md5($externalId);

        return $externalId;
    }

    public function getClearUrl(string $url): string
    {
        $urlParts = parse_url($url);

        Arr::forget($urlParts, 'fragment');
        Arr::forget($urlParts, 'query');

        $url = $this->http_build_url($urlParts);

        return $url;
    }

    public function getUrlFormat(string $pageUrl, ?string $rootPageUrl = null): string
    {
        $pageUrl = strtolower($pageUrl);
        $pageUrl = trim($pageUrl);
        /** @var array<string, mixed> $pageUrlParts */
        $pageUrlParts = parse_url($pageUrl);

        /** @var string $pageUrlPath */
        $pageUrlPath = Arr::get($pageUrlParts, 'path', '');
        $pageUrlPath = trim($pageUrlPath, '/');
        $pageUrlHost = Arr::get($pageUrlParts, 'host');

        if (empty($pageUrlPath)) {
            $pageUrlPath = '/';
        } else {
            $pageUrlPath = sprintf('/%s/', $pageUrlPath);
        }

        Arr::set($pageUrlParts, 'path', $pageUrlPath);
        Arr::forget($pageUrlParts, 'fragment');

        if (empty($pageUrlHost) && !empty($rootPageUrl)) {
            $rootPageUrl = strtolower($rootPageUrl);
            $rootPageUrl = trim($rootPageUrl);
            /** @var array<string, mixed> $rootPageUrlParts */
            $rootPageUrlParts = parse_url($rootPageUrl);
            /** @var string $rootPageUrlScheme */
            $rootPageUrlScheme = Arr::get($rootPageUrlParts, 'scheme');
            /** @var string $rootPageUrlScheme */
            $rootPageUrlHost = Arr::get($rootPageUrlParts, 'host');

            Arr::set($pageUrlParts, 'scheme', $rootPageUrlScheme);
            Arr::set($pageUrlParts, 'host', $rootPageUrlHost);
        }

        $pageUrl = $this->http_build_url($pageUrlParts);

        return $pageUrl;
    }

    private function getUrlKey(string $url): string
    {
        /** @var array<string, mixed> $urlParts */
        $urlParts = parse_url($url);

        Arr::forget($urlParts, 'fragment');
        Arr::forget($urlParts, 'scheme');
        Arr::forget($urlParts, 'host');

        $key = $this->http_build_url($urlParts);

        return $key;
    }

    private function isUrl(string $url): bool
    {
        $url = trim($url);

        if (substr_count($url, ':') > 1) {
            return false;
        }

        $isUrl = str_starts_with($url, 'http://')
            || str_starts_with($url, 'https://')
            || str_starts_with($url, '/');

        return $isUrl;
    }

    private function isFile(string $url): bool
    {
        /** @var array<string, mixed> $urlParts */
        $urlParts = parse_url($url);

        /** @var string $urlPath */
        $urlPath = Arr::get($urlParts, 'path', '');
        $urlPath = rtrim($urlPath, '/');

        Arr::set($urlParts, 'path', $urlPath);
        Arr::forget($urlParts, 'fragment');
        Arr::forget($urlParts, 'query');

        $url = $this->http_build_url($urlParts);

        $urlInfo = pathinfo($url);

        $extension = Arr::get($urlInfo, 'extension');

        if (empty($extension) || in_array($extension, ['html', 'php'])) {
            return false;
        }

        return true;
    }

    /**
     * @throws ParserException
     */
    private function isExternalUrl(string $rootPageUrl, string $pageUrl): bool
    {
        /** @var array<string, mixed> $rootPageUrlParts */
        $rootPageUrlParts = parse_url($rootPageUrl);
        /** @var string $rootPageUrlHost */
        $rootPageUrlHost = Arr::get($rootPageUrlParts, 'host');
        $rootPageUrlHost = trim($rootPageUrlHost);

        /** @var array<string, mixed> $pageUrlParts */
        $pageUrlParts = parse_url($pageUrl);
        /** @var string $pageUrlHost */
        $pageUrlHost = Arr::get($pageUrlParts, 'host');
        $pageUrlHost = trim($pageUrlHost);

        if (empty($rootPageUrlHost) || empty($pageUrlHost)) {
            throw new ParserException('Host is empty');
        }

        return $rootPageUrlHost !== $pageUrlHost;
    }

    /**
     * @param array<string, string|int> $parts
     */
    private function http_build_url(array $parts): string
    {
        return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
            (isset($parts['host']) ? '//' : '') .
            (isset($parts['host']) ? "{$parts['host']}" : '') .
            (isset($parts['port']) ? ":{$parts['port']}" : '') .
            (isset($parts['path']) ? "{$parts['path']}" : '') .
            (isset($parts['query']) ? "?{$parts['query']}" : '') .
            (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
    }
}
