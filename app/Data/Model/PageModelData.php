<?php

declare(strict_types=1);

namespace App\Data\Model;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class PageModelData extends Data
{
    public function __construct(
        public int $resourceId,
        public string $url,
        public bool $isActive,
        public string $externalId,
        public ?string $metaTitle,
        public ?string $metaDescription,
        public ?string $title,
        public string $content,
        public ?string $synonyms,
        public Carbon $parsedAt,
        public Carbon $changedAt,
        public string $contentId,
    ) {
    }
}
