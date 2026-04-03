<?php

namespace Mazhar\BDGeoLocation\Tests;

use Mazhar\BDGeoLocation\Facades\BDGeo;
use Mazhar\BDGeoLocation\Services\GeoService;
use Orchestra\Testbench\TestCase;
use Mazhar\BDGeoLocation\BDGeoServiceProvider;

class GeoServiceTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            BDGeoServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'BDGeo' => BDGeo::class,
        ];
    }

    public function test_get_all_divisions(): void
    {
        $divisions = BDGeo::getAllDivisions();

        $this->assertIsArray($divisions);
        $this->assertCount(8, $divisions);
        $this->assertArrayHasKey('id', $divisions[0]);
        $this->assertArrayHasKey('name', $divisions[0]);
        $this->assertArrayHasKey('name_bn', $divisions[0]);
    }

    public function test_get_division_by_id(): void
    {
        $division = BDGeo::getDivisionById('30');

        $this->assertIsArray($division);
        $this->assertEquals('30', $division['id']);
        $this->assertEquals('Dhaka', $division['name']);
    }

    public function test_get_districts_by_division(): void
    {
        $districts = BDGeo::getDistrictsByDivision('30');

        $this->assertIsArray($districts);
        $this->assertGreaterThan(0, count($districts));
    }

    public function test_search_by_name(): void
    {
        $results = BDGeo::searchByName('Dhaka');

        $this->assertIsArray($results);
        $this->assertGreaterThan(0, count($results));
    }

    public function test_get_geo_hierarchy(): void
    {
        $hierarchy = BDGeo::getGeoHierarchy('303427', 'upazila');

        $this->assertIsArray($hierarchy);
        $this->assertArrayHasKey('division', $hierarchy);
        $this->assertArrayHasKey('district', $hierarchy);
        $this->assertArrayHasKey('upazila', $hierarchy);
    }

    public function test_facade_works(): void
    {
        $divisions = \BDGeo::getAllDivisions();

        $this->assertIsArray($divisions);
        $this->assertCount(8, $divisions);
    }
}
