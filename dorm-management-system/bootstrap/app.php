<?php

use App\Http\Middleware\Web\AdminMiddleware;
use App\Http\Middleware\Web\LanguageMiddleware;
use App\Http\Middleware\Api\RoleMiddleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php', // Добавлен путь для API маршрутов
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Добавляем миддлвар для API маршрутов
        $middleware->alias([
            'auth'  => Authenticate::class,
            'role'  => RoleMiddleware::class,
            'admin' => AdminMiddleware::class,
            'language' => LanguageMiddleware::class,
        ]);

        // Группа миддлваров для веб-запросов
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\Web\LanguageMiddleware::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Группа миддлваров для API-запросов
        $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class, // Для замены маршрутов по ID
            \Illuminate\Cookie\Middleware\EncryptCookies::class,  // Для шифрования куки
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class, // Добавление куки в ответ
            \Illuminate\Session\Middleware\StartSession::class, // Старт сессии
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api', // Ограничение запросов
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        // Ваши настройки исключений (если нужны)
    })
    ->create();
