<?php

declare(strict_types=1);

namespace App\Services\PageParsingService;

final class BtpensiiPageParsingService extends BasePageParsingService
{
    protected const array CLASSES_TO_EXCLUDE = [
        '.bt-modal',
        '.breadcrumb',
        '.filters',
        '.sidebar .left',
        '.sidebar .right',
        '.sidebar .grid .center .bt-box-ad-06',
        '.bt-file-list',
    ];
}
