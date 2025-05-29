<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Collection::macro('initializeEnumValues', function () {
            return $this->map(function ($item) {
                if (method_exists($item, 'initializeEnumValues')) {
                    return $item->initializeEnumValues();
                }
                return $item;
            });
        });
    }
}
