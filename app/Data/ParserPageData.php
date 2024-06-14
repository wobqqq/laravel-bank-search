<?php

declare(strict_types=1);

namespace App\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class ParserPageData extends Data
{
    public function __construct(
        public string $url,
        public string $externalId,
        public int $resourceId,
        public Carbon $parsedAt,
        public ?string $metaTitle,
        public ?string $metaDescription,
        public ?string $title,
        public string $content,
        public string $contentId,
        public Carbon $changedAt,
    ) {
    }
}
