<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string|null $sitemap_url
 * @property bool $is_active
 * @method static \Illuminate\Database\Eloquent\Builder|Resource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource query()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereSitemapUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereUrl($value)
 * @mixin \Eloquent
 */
class Resource extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var array<string, string> */
    public $casts = ['is_active' => 'bool'];

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'url',
        'is_active',
        'sitemap_url',
    ];
}
