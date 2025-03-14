<?php

namespace App\Providers;

use App\Console\Commands\GenerateSitemap;
use App\Services\PurchaseOrderService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PurchaseOrderService::class, function ($app) {
            return new PurchaseOrderService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateSitemap::class,
            ]);
        }
    }
}
