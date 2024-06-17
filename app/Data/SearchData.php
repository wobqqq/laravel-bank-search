<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class SearchData extends Data
{
    public function __construct(
        public string $query,
        public ?string $resourceName = null,
        public int $perPage = 10,
    ) {
    }
}
