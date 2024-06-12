<?php

declare(strict_types=1);

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::prefix('page')
    ->controller(PageController::class)
    ->group(static function () {
        Route::get('/search', 'search');
    });