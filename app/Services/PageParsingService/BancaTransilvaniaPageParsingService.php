<?php

declare(strict_types=1);

namespace App\Services\PageParsingService;

use App\Exceptions\ParserException;

final class BancaTransilvaniaPageParsingService extends BasePageParsingService
{
    protected const array CLASSES_TO_EXCLUDE = [
        '.bt-modal',
        '.bt-article-page-contact',
        '.bt-default-grid-aside',
        '.bt-page-sticky',
        '.bt-breadcrumbs',
        '.bt-page-title p',
        '#masonryList',
        '.breadcrumb',
        '.filters',
        '.bt-main-menu-links',
        '.bt-category-listing',
        '[data-ajax-partial="@content"]',
        '.bt-header-pj-slider',
        '.bt-homepage-articles',
    ];

    /**
     * @throws ParserException
     */
    protected function check(string $urlKey): void
    {
        $this->checkNewsUrl($urlKey);
    }

    /**
     * @throws ParserException
     */
    protected function checkNewsUrl(string $urlKey): void
    {
        if (!str_starts_with($urlKey, '/news/')) {
            throw new ParserException('The page does not contain news');
        }
    }
}
