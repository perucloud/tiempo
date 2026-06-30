<?php

use App\Http\Middleware\EnsureAdminAccess;
use App\Http\Middleware\EnsureCategoryManagementAccess;
use App\Http\Middleware\EnsureUserManagementAccess;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('admin.login'));

        $middleware->alias([
            'admin.access' => EnsureAdminAccess::class,
            'admin.categories' => EnsureCategoryManagementAccess::class,
            'admin.users' => EnsureUserManagementAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Endpoint no encontrado.',
                    errors: ['route' => ['La ruta solicitada no existe.']],
                    status: 404,
                );
            }

            return null;
        });
    })->create();
