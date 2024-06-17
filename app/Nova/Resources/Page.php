<?php

declare(strict_types=1);

namespace App\Nova\Resources;

use App\Nova\Filters\IsActiveFilter;
use App\Nova\Filters\ResourceFilter;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @extends BaseResource<\App\Models\Page>
 */
class Page extends BaseResource
{
    /** @var class-string<\App\Models\Page> */
    public static string $model = \App\Models\Page::class;

    /** @var string */
    public static $title = 'name';

    /** @var array<int, string> */
    public static $search = [
        'id',
        'url',
        'external_id',
        'content_id',
        'meta_title',
        'meta_description',
        'title',
        'content',
        'synonyms',
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
            Text::make('URL')
                ->sortable()
                ->readonly(),
            Textarea::make('Synonyms')
                ->onlyOnForms(),
            Text::make('Meta title')
                ->sortable()
                ->readonly(),
            Textarea::make('Meta description')
                ->readonly()
                ->onlyOnForms(),
            Text::make('Title')
                ->sortable()
                ->readonly(),
            Textarea::make('Content')
                ->onlyOnForms()
                ->readonly(),
            BelongsTo::make('Resource')
                ->sortable()
                ->readonly(),
            Text::make('External id')
                ->readonly()
                ->onlyOnForms(),
            Text::make('Content id')
                ->readonly()
                ->onlyOnForms(),
            DateTime::make('Parsed at')
                ->sortable()
                ->readonly(),
            DateTime::make('Changed at')
                ->sortable()
                ->readonly(),
            DateTime::make('Created At')
                ->sortable()
                ->readonly(),
            DateTime::make('Updated At')
                ->sortable()
                ->readonly(),
        ];
    }

    /**
     * @return array<int, Filter>
     */
    public function filters(NovaRequest $request): array
    {
        return [
            new IsActiveFilter(),
            new ResourceFilter(),
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
