<?php

use App\Constants\ResponseMessages;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                $errors = $e->errors();
                $firstErrorMessage = collect($errors)->flatten()->first();

                return response()->error(422, $firstErrorMessage, $e->errors());
            }
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->error(401, "Unauthorized");
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {

                return response()->error(404, ResponseMessages::NOT_FOUND);
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
        if ($request->expectsJson()) {
                $message = ResponseMessages::GENERAL_FAILURE;


                if (config('app.debug')) {
                    $message = $e->getMessage();
                }

               return response()->error(500, $message);
            }
        });
    })->create();
