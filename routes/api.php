<?php

declare(strict_types=1);

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::prefix('pages')
    ->controller(PageController::class)
    ->group(function () {
        Route::get('/search', 'search')->middleware('throttle:100,1');
    });
