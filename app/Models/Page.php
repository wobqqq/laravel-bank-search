<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property string $url
 * @property bool $is_active
 * @property int $resource_id
 * @property string $external_id
 * @property string $content_id
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $title
 * @property string $content
 * @property string|null $synonyms
 * @property \Illuminate\Support\Carbon $parsed_at
 * @property \Illuminate\Support\Carbon $changed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Resource $resource
 * @method static \Illuminate\Database\Eloquent\Builder|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereContentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereParsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereSynonyms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereUrl($value)
 * @mixin \Eloquent
 */
class Page extends Model
{
    /** @var array<string, string> */
    public $casts = [
        'is_active' => 'bool',
        'parsed_at' => 'datetime',
        'changed_at' => 'datetime',
    ];

    /** @var array<int, string> */
    protected $fillable = [
        'url',
        'is_active',
        'resource_id',
        'external_id',
        'content_id',
        'meta_title',
        'meta_description',
        'title',
        'content',
        'synonyms',
        'parsed_at',
        'changed_at',
    ];

    /**
     * @return BelongsTo<Resource, Page>
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
