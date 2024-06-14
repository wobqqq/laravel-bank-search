<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Page;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final readonly class PageQuery
{
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
}
