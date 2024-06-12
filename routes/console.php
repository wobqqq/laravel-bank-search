<?php

declare(strict_types=1);

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (Command $command) {
    $command->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
