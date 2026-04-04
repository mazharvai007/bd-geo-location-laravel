<?php

namespace Mazharvai\BDGeoLocation\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Mazharvai\BDGeoLocation\Services\GeoService;

class CacheGeoDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bd-geo:cache
                            {--clear : Clear the cached geo data}
                            {--warm : Warm up the cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage Bangladesh geo location data cache';

    protected GeoService $geoService;

    public function __construct(GeoService $geoService)
    {
        parent::__construct();
        $this->geoService = $geoService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('clear')) {
            return $this->clearCache();
        }

        if ($this->option('warm')) {
            return $this->warmCache();
        }

        $this->info('BD Geo Location Cache Management');
        $this->line('Usage:');
        $this->line('  php artisan bd-geo:cache --clear    Clear cached geo data');
        $this->line('  php artisan bd-geo:cache --warm     Warm up the cache');

        return self::SUCCESS;
    }

    protected function clearCache(): int
    {
        $this->info('Clearing BD geo location cache...');

        $this->geoService->clearCache();

        $this->info('✓ Cache cleared successfully!');

        return self::SUCCESS;
    }

    protected function warmCache(): int
    {
        $this->info('Warming up BD geo location cache...');

        // Trigger cache loading by calling any method
        $divisions = $this->geoService->getAllDivisions();

        $this->info('✓ Cache warmed successfully!');
        $this->line('Cached ' . count($divisions) . ' divisions');

        return self::SUCCESS;
    }
}
