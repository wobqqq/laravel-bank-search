<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Resource;
use Illuminate\Support\Collection;

final readonly class ResourceQuery
{
    /**
     * @return Collection<int, Resource>
     */
    public function getActiveResources(): Collection
    {
        return Resource::whereIsActive(true)->get();
    }
}
