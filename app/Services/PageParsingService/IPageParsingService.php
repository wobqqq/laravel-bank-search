<?php

declare(strict_types=1);

namespace App\Services\PageParsingService;

use App\Models\Resource;
use Symfony\Component\DomCrawler\Crawler;

interface IPageParsingService
{
    public function serve(string $urlKey, Crawler $crawler, Resource $resource): void;
}
