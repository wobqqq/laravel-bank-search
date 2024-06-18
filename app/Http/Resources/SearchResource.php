<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    /** @var Page */
    public $resource;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'url' => $this->resource->url,
            'title' => $this->resource->title ?? $this->resource->meta_title,
            'content' => $this->resource->content,
        ];
    }
}
