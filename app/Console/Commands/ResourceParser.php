<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResourceParser extends Command
{
    /** @var string */
    protected $signature = 'app:resource-parser';

    /** @var string */
    protected $description = 'Resource parsing';

    public function handle(): void
    {
    }
}
