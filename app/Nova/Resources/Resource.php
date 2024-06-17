<?php

declare(strict_types=1);

namespace App\Nova\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @extends BaseResource<\App\Models\Resource>
 */
class Resource extends BaseResource
{
    /** @var class-string<\App\Models\Resource> */
    public static string $model = \App\Models\Resource::class;

    /** @var string */
    public static $title = 'name';

    /** @var array<int, string> */
    public static $search = [
        'id',
        'name',
        'url',
    ];

    /**
     * @return array<int, Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable()
                ->onlyOnForms()
                ->readonly(),
            Boolean::make('Is Active')
                ->sortable(),
            Text::make('Name')
                ->sortable()
                ->readonly(),
            Text::make('URL')
                ->sortable()
                ->rules('required', 'max:255'),
            Text::make('Sitemap URL')
                ->sortable()
                ->rules('nullable', 'max:255'),
        ];
    }

    public function authorizedToView(Request $request): bool
    {
        return false;
    }

    public function authorizedToDelete(Request $request): bool
    {
        return false;
    }

    public function authorizedToReplicate(Request $request): bool
    {
        return false;
    }

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }
}
