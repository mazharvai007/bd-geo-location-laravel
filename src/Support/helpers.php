<?php

use Mazhar\BDGeoLocation\Facades\BDGeo;

if (!function_exists('bd_divisions')) {
    /**
     * Get all divisions.
     *
     * @return array<int, array>
     */
    function bd_divisions(): array
    {
        return BDGeo::getAllDivisions();
    }
}

if (!function_exists('bd_division')) {
    /**
     * Get division by ID.
     */
    function bd_division(string $id): ?array
    {
        return BDGeo::getDivisionById($id);
    }
}

if (!function_exists('bd_districts')) {
    /**
     * Get districts by division ID.
     *
     * @return array<int, array>
     */
    function bd_districts(?string $divisionId = null): array
    {
        if ($divisionId === null) {
            return BDGeo::getAllDistricts();
        }

        return BDGeo::getDistrictsByDivision($divisionId);
    }
}

if (!function_exists('bd_district')) {
    /**
     * Get district by ID.
     */
    function bd_district(string $id): ?array
    {
        return BDGeo::getDistrictById($id);
    }
}

if (!function_exists('bd_upazilas')) {
    /**
     * Get upazilas by district ID.
     *
     * @return array<int, array>
     */
    function bd_upazilas(?string $districtId = null): array
    {
        if ($districtId === null) {
            return BDGeo::getAllUpazilas();
        }

        return BDGeo::getUpazilasByDistrict($districtId);
    }
}

if (!function_exists('bd_upazila')) {
    /**
     * Get upazila by ID.
     */
    function bd_upazila(string $id): ?array
    {
        return BDGeo::getUpazilaById($id);
    }
}

if (!function_exists('bd_unions')) {
    /**
     * Get unions by upazila ID.
     *
     * @return array<int, array>
     */
    function bd_unions(?string $upazilaId = null): array
    {
        if ($upazilaId === null) {
            return BDGeo::getAllUnions();
        }

        return BDGeo::getUnionsByUpazila($upazilaId);
    }
}

if (!function_exists('bd_union')) {
    /**
     * Get union by ID.
     */
    function bd_union(string $id): ?array
    {
        return BDGeo::getUnionById($id);
    }
}

if (!function_exists('bd_geo_search')) {
    /**
     * Search locations by name.
     *
     * @return array<int, array>
     */
    function bd_geo_search(string $term): array
    {
        return BDGeo::searchByName($term);
    }
}

if (!function_exists('bd_geo_hierarchy')) {
    /**
     * Get complete hierarchy for a location.
     */
    function bd_geo_hierarchy(string $id, string $type): ?array
    {
        return BDGeo::getGeoHierarchy($id, $type);
    }
}
