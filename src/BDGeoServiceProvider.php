<?php

namespace Mazhar\BDGeoLocation;

use Illuminate\Support\ServiceProvider;
use Mazhar\BDGeoLocation\Commands\SeedGeoDataCommand;
use Mazhar\BDGeoLocation\Commands\CacheGeoDataCommand;
use Mazhar\BDGeoLocation\Services\GeoService;

class BDGeoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration file
        $this->publishes([
            __DIR__ . '/../config/bd-geo.php' => config_path('bd-geo.php'),
        ], 'bd-geo-config');

        // Publish database migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'bd-geo-migrations');

        // Publish seeders
        $this->publishes([
            __DIR__ . '/../database/seeders' => database_path('seeders'),
        ], 'bd-geo-seeders');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                SeedGeoDataCommand::class,
                CacheGeoDataCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/bd-geo.php',
            'bd-geo'
        );

        // Register GeoService as singleton
        $this->app->singleton(GeoService::class, function ($app) {
            return new GeoService();
        });

        // Register alias
        $this->app->alias(GeoService::class, 'bd-geo');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [GeoService::class, 'bd-geo'];
    }
}
