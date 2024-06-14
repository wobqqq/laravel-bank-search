<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class TypeData extends Data
{
    public function __construct(
        public string $string,
        public string $integer,
    ) {
    }
}
