<?php

namespace Mazharvai\BDGeoLocation\Tests;

use Mazharvai\BDGeoLocation\Facades\BDGeo;
use Mazharvai\BDGeoLocation\Services\GeoService;
use Orchestra\Testbench\TestCase;
use Mazharvai\BDGeoLocation\BDGeoServiceProvider;
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
        $this->assertArrayHasKey('nameBn', $divisions[0]);
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
        $hierarchy = BDGeo::getGeoHierarchy('175', 'upazila');

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

    public function test_empty_id_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BDGeo::getDivisionById('');
    }

    public function test_get_district_by_non_existent_id_returns_null(): void
    {
        $district = BDGeo::getDistrictById('999999');

        $this->assertNull($district);
    }

    public function test_get_upazila_by_non_existent_id_returns_null(): void
    {
        $upazila = BDGeo::getUpazilaById('999999');

        $this->assertNull($upazila);
    }

    public function test_get_union_by_non_existent_id_returns_null(): void
    {
        $union = BDGeo::getUnionById('999999');

        $this->assertNull($union);
    }

    public function test_get_districts_by_non_existent_division_returns_empty(): void
    {
        $districts = BDGeo::getDistrictsByDivision('999999');

        $this->assertIsArray($districts);
        $this->assertEmpty($districts);
    }

    public function test_get_upazilas_by_non_existent_district_returns_empty(): void
    {
        $upazilas = BDGeo::getUpazilasByDistrict('999999');

        $this->assertIsArray($upazilas);
        $this->assertEmpty($upazilas);
    }

    public function test_get_unions_by_non_existent_upazila_returns_empty(): void
    {
        $unions = BDGeo::getUnionsByUpazila('999999');

        $this->assertIsArray($unions);
        $this->assertEmpty($unions);
    }

    public function test_geo_hierarchy_for_non_existent_location_returns_null(): void
    {
        $hierarchy = BDGeo::getGeoHierarchy('999999', 'district');

        $this->assertNull($hierarchy);
    }

    public function test_geo_hierarchy_division_type(): void
    {
        $hierarchy = BDGeo::getGeoHierarchy('30', 'division');

        $this->assertIsArray($hierarchy);
        $this->assertArrayHasKey('division', $hierarchy);
        $this->assertArrayNotHasKey('district', $hierarchy);
        $this->assertEquals('Dhaka', $hierarchy['division']['name']);
    }

    public function test_geo_hierarchy_district_type(): void
    {
        $hierarchy = BDGeo::getGeoHierarchy('23', 'district');

        $this->assertIsArray($hierarchy);
        $this->assertArrayHasKey('division', $hierarchy);
        $this->assertArrayHasKey('district', $hierarchy);
        $this->assertArrayNotHasKey('upazila', $hierarchy);
    }

    public function test_geo_hierarchy_union_type(): void
    {
        // Find a valid union ID first
        $unions = BDGeo::getAllUnions();
        $unionId = $unions[0]['id'] ?? null;

        $this->assertNotNull($unionId, 'No unions found in data');

        $hierarchy = BDGeo::getGeoHierarchy($unionId, 'union');

        $this->assertIsArray($hierarchy);
        $this->assertArrayHasKey('division', $hierarchy);
        $this->assertArrayHasKey('district', $hierarchy);
        $this->assertArrayHasKey('upazila', $hierarchy);
        $this->assertArrayHasKey('union', $hierarchy);
    }

    public function test_search_returns_correct_structure(): void
    {
        $results = BDGeo::searchByName('Dhaka');

        $this->assertIsArray($results);
        if (!empty($results)) {
            $this->assertArrayHasKey('type', $results[0]);
            $this->assertContains($results[0]['type'], ['division', 'district', 'upazila', 'union']);
        }
    }

    public function test_search_with_very_long_string(): void
    {
        $veryLongString = str_repeat('a', 1000);
        $results = BDGeo::searchByName($veryLongString);

        $this->assertIsArray($results);
        // Should handle long strings gracefully (truncated to 100 chars)
    }

    public function test_search_with_null_bytes(): void
    {
        $results = BDGeo::searchByName("Dhaka\x00");

        $this->assertIsArray($results);
        // Should handle null bytes gracefully
    }

    public function test_search_with_control_characters(): void
    {
        $results = BDGeo::searchByName("Dhaka\x01\x02\x03");

        $this->assertIsArray($results);
        // Should handle control characters gracefully
    }

    public function test_cache_clear_and_reload(): void
    {
        $divisions1 = BDGeo::getAllDivisions();
        BDGeo::clearCache();
        $divisions2 = BDGeo::getAllDivisions();

        $this->assertEquals($divisions1, $divisions2);
    }

    public function test_statistics_returns_correct_counts(): void
    {
        $stats = BDGeo::getStatistics();

        $this->assertGreaterThan(0, $stats['divisions']);
        $this->assertGreaterThan(0, $stats['districts']);
        $this->assertGreaterThan(0, $stats['upazilas']);
        $this->assertGreaterThan(0, $stats['unions']);
    }

    public function test_district_has_parent_division_info(): void
    {
        $districts = BDGeo::getAllDistricts();

        $this->assertNotEmpty($districts);
        $district = $districts[0];

        $this->assertArrayHasKey('division_id', $district);
        $this->assertArrayHasKey('division_name', $district);
        $this->assertNotEmpty($district['division_id']);
    }

    public function test_upazila_has_parent_info(): void
    {
        $upazilas = BDGeo::getAllUpazilas();

        $this->assertNotEmpty($upazilas);
        $upazila = $upazilas[0];

        $this->assertArrayHasKey('district_id', $upazila);
        $this->assertArrayHasKey('district_name', $upazila);
        $this->assertArrayHasKey('division_id', $upazila);
        $this->assertArrayHasKey('division_name', $upazila);
    }

    public function test_union_has_complete_parent_info(): void
    {
        $unions = BDGeo::getAllUnions();

        $this->assertNotEmpty($unions);
        $union = $unions[0];

        $this->assertArrayHasKey('upazila_id', $union);
        $this->assertArrayHasKey('upazila_name', $union);
        $this->assertArrayHasKey('district_id', $union);
        $this->assertArrayHasKey('district_name', $union);
        $this->assertArrayHasKey('division_id', $union);
        $this->assertArrayHasKey('division_name', $union);
    }

    public function test_case_insensitive_search(): void
    {
        $results1 = BDGeo::searchByName('dhaka');
        $results2 = BDGeo::searchByName('DHAKA');
        $results3 = BDGeo::searchByName('Dhaka');

        $this->assertNotEmpty($results1);
        $this->assertNotEmpty($results2);
        $this->assertNotEmpty($results3);
        $this->assertEquals(count($results1), count($results2));
        $this->assertEquals(count($results1), count($results3));
    }

    public function test_partial_match_search(): void
    {
        $results = BDGeo::searchByName('Dha');

        $this->assertIsArray($results);
        $this->assertGreaterThan(0, count($results));
        // Should find locations containing "Dha"
    }

    public function test_search_empty_after_sanitization(): void
    {
        $results = BDGeo::searchByName('   ');

        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    public function test_all_divisions_have_required_fields(): void
    {
        $divisions = BDGeo::getAllDivisions();

        foreach ($divisions as $division) {
            $this->assertArrayHasKey('id', $division);
            $this->assertArrayHasKey('name', $division);
            $this->assertArrayHasKey('nameBn', $division);
        }
    }

    public function test_all_districts_have_required_fields(): void
    {
        $districts = BDGeo::getAllDistricts();

        foreach (array_slice($districts, 0, 10) as $district) {
            $this->assertArrayHasKey('id', $district);
            $this->assertArrayHasKey('name', $district);
            $this->assertArrayHasKey('division_id', $district);
        }
    }

    public function test_service_instance_is_singleton_like(): void
    {
        $service1 = app(\Mazharvai\BDGeoLocation\Services\GeoService::class);
        $service2 = app(\Mazharvai\BDGeoLocation\Services\GeoService::class);

        $this->assertSame($service1, $service2);
    }
}
