<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Exceptions\Handler as AppHandler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    //     //     if (env('APP_URL')) {
    //     // URL::forceRootUrl(env('APP_URL'));
    //     // URL::forceScheme('https');
    // }

        $this->app->singleton(ExceptionHandler::class, AppHandler::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}