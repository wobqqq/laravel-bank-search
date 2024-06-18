<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    /** @var string $url */
    $url = Config::get('nova.path', '/admin');

    return Redirect::to($url);
});

Route::get('/search-example', function () {
    return view('search-example');
});
