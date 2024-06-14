<?php

declare(strict_types=1);

namespace App\Services\PageParsingService;

final class BlogBancaTransilvaniaPageParsingService extends BasePageParsingService
{
    protected const array CLASSES_TO_EXCLUDE = [
        '.bt-article-page-feedback',
        '.bt-page-sticky',
        '.bt-breadcrumbs',
        '.bt-page-title p',
        '.bt-default-grid-aside',
        '.bt-modal',
        '.modal-search',
        '.breadcrumb',
        '.filters',
        '.bt-main-menu-links',
        '.bt-category-listing',
        '.box-autor-articole',
        '[data-ajax-partial="@content"]',
    ];
}
