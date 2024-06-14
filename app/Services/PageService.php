<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Model\PageModelData;
use App\Models\Page;
use App\Queries\PageQuery;

final readonly class PageService
{
    public function __construct(private PageQuery $pageQuery)
    {
    }

    public function create(PageModelData $pageModelData): Page
    {
        return Page::create([
            'resource_id' => $pageModelData->resourceId,
            'url' => $pageModelData->url,
            'active' => $pageModelData->isActive,
            'external_id' => $pageModelData->externalId,
            'parsed_at' => $pageModelData->parsedAt,
            'changed_at' => $pageModelData->changedAt,
            'meta_title' => $pageModelData->metaTitle,
            'meta_description' => $pageModelData->metaDescription,
            'title' => $pageModelData->title,
            'content' => $pageModelData->content,
            'synonyms' => $pageModelData->synonyms,
            'content_id' => $pageModelData->contentId,
        ]);
    }

    public function update(PageModelData $pageModelData, Page $page): Page
    {
        $page->update([
            'resource_id' => $pageModelData->resourceId,
            'url' => $pageModelData->url,
            'active' => $pageModelData->isActive,
            'external_id' => $pageModelData->externalId,
            'parsed_at' => $pageModelData->parsedAt,
            'changed_at' => $pageModelData->changedAt,
            'title' => $pageModelData->title,
            'content' => $pageModelData->content,
            'meta_title' => $pageModelData->metaTitle,
            'meta_description' => $pageModelData->metaDescription,
            'synonyms' => $pageModelData->synonyms,
            'content_id' => $pageModelData->contentId,
        ]);

        return $page;
    }

    public function delete(Page $page): void
    {
        $page->delete();
    }

    /**
     * @param string[] $externalIds
     */
    public function deletePagesByExternalIds(array $externalIds): void
    {
        if (empty($externalIds)) {
            return;
        }

        $pages = $this->pageQuery->getPageUrlsByExternalIds($externalIds);

        $pages->each(function (Page $page) {
            $this->delete($page);
        });
    }
}
