<?php

declare(strict_types=1);

namespace App\Nova\Filters;

use App\Models\Page;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Nova\Filters\BooleanFilter;
use Laravel\Nova\Http\Requests\NovaRequest;

class ResourceFilter extends BooleanFilter
{
    /** @var string */
    public $name = 'Resources';

    /**
     * @param Builder<Page> $query
     * @return Builder<Page>
     */
    public function apply(NovaRequest $request, $query, $value): Builder
    {
        /** @var array<int, bool> $value */
        $resourceIds = Collection::make($value)
            ->filter(fn (bool $value, int $id) => $value)
            ->keys()
            ->toArray();

        if (empty($resourceIds)) {
            return $query;
        }

        return $query->whereIn('resource_id', $resourceIds);
    }

    /**
     * @return array<string, int>
     */
    public function options(NovaRequest $request): array
    {
        /** @var array<string, int> $options */
        $options = Resource::pluck('id', 'name')->all();

        return $options;
    }
}
