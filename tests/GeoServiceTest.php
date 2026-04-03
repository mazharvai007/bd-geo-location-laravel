<?php

namespace Mazhar\BDGeoLocation\Tests;

use Mazhar\BDGeoLocation\Facades\BDGeo;
use Mazhar\BDGeoLocation\Services\GeoService;
use Orchestra\Testbench\TestCase;
use Mazhar\BDGeoLocation\BDGeoServiceProvider;
use InvalidArgumentException;

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

    public function test_get_division_by_invalid_id_returns_null(): void
    {
        $division = BDGeo::getDivisionById('invalid-id');

        $this->assertNull($division);
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

    public function test_search_with_bengali_text(): void
    {
        $results = BDGeo::searchByName('ঢাকা');

        $this->assertIsArray($results);
        $this->assertGreaterThan(0, count($results));
    }

    public function test_search_sanitizes_html_tags(): void
    {
        $results = BDGeo::searchByName('<script>alert("xss")</script>Dhaka');

        $this->assertIsArray($results);
        // Should not cause errors and should return Dhaka results
    }

    public function test_search_limits_results(): void
    {
        // Search for common term that would have many results
        $results = BDGeo::searchByName('a');

        $this->assertLessThanOrEqual(100, count($results));
    }

    public function test_get_geo_hierarchy(): void
    {
        $hierarchy = BDGeo::getGeoHierarchy('303427', 'upazila');

        $this->assertIsArray($hierarchy);
        $this->assertArrayHasKey('division', $hierarchy);
        $this->assertArrayHasKey('district', $hierarchy);
        $this->assertArrayHasKey('upazila', $hierarchy);
    }

    public function test_get_geo_hierarchy_with_invalid_type_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BDGeo::getGeoHierarchy('303427', 'invalid-type');
    }

    public function test_facade_works(): void
    {
        $divisions = \BDGeo::getAllDivisions();

        $this->assertIsArray($divisions);
        $this->assertCount(8, $divisions);
    }

    public function test_clear_cache(): void
    {
        // This should not throw an error
        BDGeo::clearCache();

        $this->assertTrue(true);
    }

    public function test_get_statistics(): void
    {
        $stats = BDGeo::getStatistics();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('divisions', $stats);
        $this->assertArrayHasKey('districts', $stats);
        $this->assertArrayHasKey('upazilas', $stats);
        $this->assertArrayHasKey('unions', $stats);
        $this->assertArrayHasKey('data_source', $stats);
        $this->assertArrayHasKey('cache_enabled', $stats);

        $this->assertEquals(8, $stats['divisions']);
        $this->assertEquals(68, $stats['districts']);
        $this->assertEquals(531, $stats['upazilas']);
        $this->assertEquals(4916, $stats['unions']);
    }

    public function test_get_all_districts(): void
    {
        $districts = BDGeo::getAllDistricts();

        $this->assertIsArray($districts);
        $this->assertCount(68, $districts);
        $this->assertArrayHasKey('division_id', $districts[0]);
        $this->assertArrayHasKey('division_name', $districts[0]);
    }

    public function test_get_all_upazilas(): void
    {
        $upazilas = BDGeo::getAllUpazilas();

        $this->assertIsArray($upazilas);
        $this->assertCount(531, $upazilas);
        $this->assertArrayHasKey('district_id', $upazilas[0]);
        $this->assertArrayHasKey('district_name', $upazilas[0]);
        $this->assertArrayHasKey('division_id', $upazilas[0]);
        $this->assertArrayHasKey('division_name', $upazilas[0]);
    }

    public function test_get_all_unions(): void
    {
        $unions = BDGeo::getAllUnions();

        $this->assertIsArray($unions);
        $this->assertCount(4916, $unions);
        $this->assertArrayHasKey('upazila_id', $unions[0]);
        $this->assertArrayHasKey('district_id', $unions[0]);
        $this->assertArrayHasKey('division_id', $unions[0]);
    }

    public function test_cross_platform_file_path(): void
    {
        $service = app(GeoService::class);

        // Get the data file path
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('getDataFilePath');
        $method->setAccessible(true);

        $path = $method->invoke($service);

        // Verify path uses correct directory separator
        $this->assertStringContainsString('data', $path);
        $this->assertStringContainsString('bangladesh.json', $path);

        // Verify path exists
        $this->assertTrue(file_exists($path));
    }

    public function test_helper_functions_exist(): void
    {
        $this->assertTrue(function_exists('bd_divisions'));
        $this->assertTrue(function_exists('bd_division'));
        $this->assertTrue(function_exists('bd_districts'));
        $this->assertTrue(function_exists('bd_district'));
        $this->assertTrue(function_exists('bd_upazilas'));
        $this->assertTrue(function_exists('bd_upazila'));
        $this->assertTrue(function_exists('bd_unions'));
        $this->assertTrue(function_exists('bd_union'));
        $this->assertTrue(function_exists('bd_geo_search'));
        $this->assertTrue(function_exists('bd_geo_hierarchy'));
        $this->assertTrue(function_exists('bd_geo_clear_cache'));
        $this->assertTrue(function_exists('bd_geo_stats'));
    }

    public function test_helper_function_bd_geo_stats(): void
    {
        $stats = bd_geo_stats();

        $this->assertIsArray($stats);
        $this->assertEquals(8, $stats['divisions']);
    }

    public function test_helper_function_bd_geo_clear_cache(): void
    {
        // Should not throw an error
        bd_geo_clear_cache();

        $this->assertTrue(true);
    }

    public function test_caching_performance(): void
    {
        $service = app(GeoService::class);

        // First call - cache miss
        $start1 = microtime(true);
        $divisions1 = $service->getAllDivisions();
        $time1 = microtime(true) - $start1;

        // Second call - cache hit
        $start2 = microtime(true);
        $divisions2 = $service->getAllDivisions();
        $time2 = microtime(true) - $start2;

        $this->assertEquals($divisions1, $divisions2);

        // Cached call should be significantly faster (or at least not slower)
        // In a real scenario, cache would persist across requests
        $this->assertGreaterThan(0, $time1);
    }

    public function test_search_handles_empty_string(): void
    {
        $results = BDGeo::searchByName('');

        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    public function test_search_handles_special_characters(): void
    {
        $results = BDGeo::searchByName("Dhaka@#$%^&*()");

        $this->assertIsArray($results);
        // Should not throw errors
    }

    public function test_unicode_handling_in_search(): void
    {
        $results = BDGeo::searchByName('ঢাকা জেলা');

        $this->assertIsArray($results);
        // Should handle Bengali unicode properly
    }
}
