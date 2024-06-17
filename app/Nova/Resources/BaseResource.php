<?php

declare(strict_types=1);

namespace App\Nova\Resources;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource as NovaResource;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @extends NovaResource<TModel>
 */
abstract class BaseResource extends NovaResource
{
    /**
     * Build an "index" query for the given resource.
     *
     * @param  NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder<TModel>  $query
     * @return \Illuminate\Database\Eloquent\Builder<TModel>
     */
    public static function indexQuery(NovaRequest $request, $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query;
    }

    /**
     * Build a Scout search query for the given resource.
     *
     * @param  NovaRequest  $request
     * @param  \Laravel\Scout\Builder  $query
     * @return \Laravel\Scout\Builder
     */
    public static function scoutQuery(NovaRequest $request, $query): \Laravel\Scout\Builder
    {
        return $query;
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param  NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder<TModel>  $query
     * @return \Illuminate\Database\Eloquent\Builder<TModel>
     */
    public static function detailQuery(NovaRequest $request, $query): \Illuminate\Database\Eloquent\Builder
    {
        return parent::detailQuery($request, $query);
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder<TModel>  $query
     * @return \Illuminate\Database\Eloquent\Builder<TModel>
     */
    public static function relatableQuery(NovaRequest $request, $query): \Illuminate\Database\Eloquent\Builder
    {
        return parent::relatableQuery($request, $query);
    }
}
