<?php

namespace Mazharvai\BDGeoLocation\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Mazharvai\BDGeoLocation\Services\GeoService;

class SeedGeoDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bd-geo:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed Bangladesh geo location data to database';

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
        $this->info('Starting to seed Bangladesh geo data...');

        try {
            // Get table prefix from config
            $prefix = config('bd-geo.table_prefix', 'bd_');

            $this->info('Seeding divisions...');
            $this->seedDivisions($prefix);

            $this->info('Seeding districts...');
            $this->seedDistricts($prefix);

            $this->info('Seeding upazilas...');
            $this->seedUpazilas($prefix);

            $this->info('Seeding unions...');
            $this->seedUnions($prefix);

            $this->info('Bangladesh geo data seeded successfully!');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error seeding geo data: ' . $e->getMessage());

            return self::FAILURE;
        }
    }

    protected function seedDivisions(string $prefix): void
    {
        $divisions = $this->geoService->getAllDivisions();

        foreach ($divisions as $division) {
            DB::table($prefix . 'divisions')->updateOrInsert(
                ['id' => $division['id']],
                [
                    'name' => $division['name'],
                    'name_bn' => $division['name_bn'],
                    'lat' => $division['lat'] ?? null,
                    'long' => $division['long'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->line("✓ Seeded " . count($divisions) . " divisions");
    }

    protected function seedDistricts(string $prefix): void
    {
        $districts = $this->geoService->getAllDistricts();

        foreach ($districts as $district) {
            DB::table($prefix . 'districts')->updateOrInsert(
                ['id' => $district['id']],
                [
                    'division_id' => $district['division_id'],
                    'name' => $district['name'],
                    'name_bn' => $district['name_bn'],
                    'lat' => $district['lat'] ?? null,
                    'long' => $district['long'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->line("✓ Seeded " . count($districts) . " districts");
    }

    protected function seedUpazilas(string $prefix): void
    {
        $upazilas = $this->geoService->getAllUpazilas();

        foreach ($upazilas as $upazila) {
            DB::table($prefix . 'upazilas')->updateOrInsert(
                ['id' => $upazila['id']],
                [
                    'district_id' => $upazila['district_id'],
                    'name' => $upazila['name'],
                    'name_bn' => $upazila['name_bn'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->line("✓ Seeded " . count($upazilas) . " upazilas");
    }

    protected function seedUnions(string $prefix): void
    {
        $unions = $this->geoService->getAllUnions();

        $bar = $this->output->createProgressBar(count($unions));
        $bar->start();

        foreach ($unions as $union) {
            DB::table($prefix . 'unions')->updateOrInsert(
                ['id' => $union['id']],
                [
                    'upazila_id' => $union['upazila_id'],
                    'name' => $union['name'],
                    'name_bn' => $union['name_bn'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->line("✓ Seeded " . count($unions) . " unions");
    }
}
