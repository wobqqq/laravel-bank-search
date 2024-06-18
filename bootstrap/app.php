<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpStatus;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            if ($e instanceof ValidationException || $e instanceof ThrottleRequestsException) {
                return response()->json(['message' => $e->getMessage()], HttpStatus::HTTP_UNPROCESSABLE_ENTITY);
            }

            return response()->json(['message' => 'Server error'], HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();
