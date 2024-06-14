<?php

declare(strict_types=1);

namespace App\Services\PageParsingService;

final class BtmicPageParsingService extends BasePageParsingService
{
    protected const array CLASSES_TO_EXCLUDE = [
        '#map',
        '#big_search',
        '.articole-home',
        '.back-btmic',
        '.share',
        '.breadcrumb',
        '.header-blog--carousel',
        '.articole #btnMore',
    ];
}
