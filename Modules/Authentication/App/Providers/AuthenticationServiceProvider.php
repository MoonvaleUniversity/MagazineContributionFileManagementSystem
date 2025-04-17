<?php

namespace Modules\Authentication\App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Authentication\Services\AuthenticationServiceInterface;
use Modules\Authentication\Services\Implementations\AuthenticationService;

class AuthenticationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthenticationServiceInterface::class, AuthenticationService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::prefix('api/v1')
            ->middleware('api') // Apply any middleware if needed
            ->group(function () {
                require __DIR__ . '/../../routes/api_v1.0.php';
            });
        Route::middleware('web')->group(function () {
            require __DIR__ . '/../../routes/web.php';
        });
    }
}
