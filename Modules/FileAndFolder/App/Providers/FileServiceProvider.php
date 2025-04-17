<?php

namespace Modules\FileAndFolder\App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FileServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // $this->app->bind(AuthenticationServiceInterface::class, AuthenticationService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::middleware('web')->group(function () {
            require __DIR__ . '/../../routes/web.php';
        });
    }
}
