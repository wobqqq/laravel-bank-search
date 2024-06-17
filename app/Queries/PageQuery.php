<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Page;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final readonly class PageQuery
{
    /** @var array<string, float|int> */
    private const array SEARCH_SCORE = [
        'meta_title' => 1,
        'synonyms' => 1,
        'meta_description' => 0.8,
        'title' => 0.7,
        'content' => 0.6,
    ];

    /**
     * @return array<string, string>
     */
    public function getPageUrlsByResource(Resource $resource): array
    {
        /** @var array<string, string> $urls */
        $urls = Page::whereresourceId($resource->id)->pluck('url', 'url')->toArray();

        return $urls;
    }

    /**
     * @param array<string, string> $externalIds
     * @return Collection<int, Page>
     */
    public function getPageUrlsByExternalIds(array $externalIds): Collection
    {
        return Page::whereIn('external_id', $externalIds)->get();
    }

    public function isDuplicatePageContent(?Page $page, string $contentId): bool
    {
        $page = Page::whereContentId($contentId)
            ->when($page, function (Builder $query, Page $page) {
                return $query->where('id', '!=', $page->id);
            })
            ->first();

        return !empty($page);
    }

    /**
     * @return LengthAwarePaginator<Page>
     */
    public function search(string $query, int $perPage, int $resourceId = null): LengthAwarePaginator
    {
        $search_score = Collection::make(self::SEARCH_SCORE);
        $search_score = $search_score->map(function (float|int $score, string $column) {
            return sprintf('(MATCH(%s) AGAINST(? IN NATURAL LANGUAGE MODE) * %s)', $column, $score);
        });
        $search_score = implode(' + ', $search_score->toArray());

        $expression = "
            pages.url,
            pages.title,
            pages.meta_title,
            pages.content,
            ($search_score) AS score
        ";

        $sql = 'MATCH(title, content, synonyms, meta_title, meta_description) AGAINST(? IN NATURAL LANGUAGE MODE)';

        $pages = Page::whereIsActive(true)
            ->whereHas('resource', fn (Builder|Resource $q) => $q->whereIsActive(true))
            ->when($resourceId, function (Builder|Page $query, int $resourceId) {
                return $query->where('resource_id', $resourceId);
            })
            ->selectRaw($expression, [$query, $query, $query, $query, $query])
            ->whereRaw($sql, [$query])
            ->orderByDesc('score')
            ->paginate($perPage);

        return $pages;
    }
}
