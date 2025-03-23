<?php

namespace App\Providers;

use App\Helpers\PostcodeLocationLoaders\MySocietyPostcodeLocationLoader;
use App\Helpers\PostcodeLocationLoaders\PostcodeLocationLoader;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PostcodeLocationLoader::class, function () {
            return new MySocietyPostcodeLocationLoader();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
