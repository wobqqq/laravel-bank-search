<?php

declare(strict_types=1);

namespace App\Nova\Filters;

use App\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class IsActiveFilter extends Filter
{
    /** @var string */
    public $name = 'Is Active';

    /**
     * @param Builder<Page> $query
     * @return Builder<Page>
     */
    public function apply(NovaRequest $request, $query, $value): Builder
    {
        /** @var string $value */
        return $query->where('is_active', (int)$value);
    }

    /**
     * @return array<string, int>
     */
    public function options(NovaRequest $request): array
    {
        return [
            'Yes' => 1,
            'No' => 0,
        ];
    }
}
