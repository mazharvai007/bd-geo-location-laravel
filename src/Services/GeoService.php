<?php

namespace Mazhar\BDGeoLocation\Services;

use Illuminate\Support\Facades\Cache;

class GeoService
{
    protected array $data = [];

    protected string $dataSource;

    protected int $cacheDuration;

    public function __construct()
    {
        $this->dataSource = config('bd-geo.data_source', 'json');
        $this->cacheDuration = config('bd-geo.cache_duration', 604800); // 7 days default

        $this->loadData();
    }

    /**
     * Load geo data based on configuration
     */
    protected function loadData(): void
    {
        if ($this->dataSource === 'database') {
            $this->loadFromDatabase();
        } else {
            $this->loadFromJson();
        }
    }

    /**
     * Load data from JSON file
     */
    protected function loadFromJson(): void
    {
        $cacheKey = 'bd-geo-location-data';

        $this->data = Cache::remember($cacheKey, $this->cacheDuration, function () {
            $jsonPath = dirname(__DIR__, 2) . '/data/bangladesh.json';

            if (!file_exists($jsonPath)) {
                return [];
            }

            $jsonContent = file_get_contents($jsonPath);
            $data = json_decode($jsonContent, true);

            return $data ?? [];
        });
    }

    /**
     * Load data from database
     */
    protected function loadFromDatabase(): void
    {
        // This will be used when database models are implemented
        // For now, fall back to JSON
        $this->loadFromJson();
    }

    /**
     * Get all divisions
     *
     * @return array<int, array>
     */
    public function getAllDivisions(): array
    {
        return $this->data['divisions'] ?? [];
    }

    /**
     * Get division by ID
     */
    public function getDivisionById(string $id): ?array
    {
        $divisions = $this->getAllDivisions();

        foreach ($divisions as $division) {
            if ($division['id'] === $id) {
                return $division;
            }
        }

        return null;
    }

    /**
     * Get all districts
     *
     * @return array<int, array>
     */
    public function getAllDistricts(): array
    {
        $districts = [];

        foreach ($this->data['divisions'] ?? [] as $division) {
            foreach ($division['districts'] ?? [] as $district) {
                $districts[] = array_merge($district, [
                    'division_id' => $division['id'],
                    'division_name' => $division['name'],
                    'division_name_bn' => $division['name_bn'],
                ]);
            }
        }

        return $districts;
    }

    /**
     * Get districts by division ID
     *
     * @return array<int, array>
     */
    public function getDistrictsByDivision(string $divisionId): array
    {
        $division = $this->getDivisionById($divisionId);

        if (!$division) {
            return [];
        }

        return $division['districts'] ?? [];
    }

    /**
     * Get district by ID
     */
    public function getDistrictById(string $id): ?array
    {
        $districts = $this->getAllDistricts();

        foreach ($districts as $district) {
            if ($district['id'] === $id) {
                return $district;
            }
        }

        return null;
    }

    /**
     * Get all upazilas
     *
     * @return array<int, array>
     */
    public function getAllUpazilas(): array
    {
        $upazilas = [];

        foreach ($this->data['divisions'] ?? [] as $division) {
            foreach ($division['districts'] ?? [] as $district) {
                foreach ($district['upazilas'] ?? [] as $upazila) {
                    $upazilas[] = array_merge($upazila, [
                        'district_id' => $district['id'],
                        'district_name' => $district['name'],
                        'division_id' => $division['id'],
                        'division_name' => $division['name'],
                    ]);
                }
            }
        }

        return $upazilas;
    }

    /**
     * Get upazilas by district ID
     *
     * @return array<int, array>
     */
    public function getUpazilasByDistrict(string $districtId): array
    {
        $district = $this->getDistrictById($districtId);

        if (!$district) {
            return [];
        }

        return $district['upazilas'] ?? [];
    }

    /**
     * Get upazila by ID
     */
    public function getUpazilaById(string $id): ?array
    {
        $upazilas = $this->getAllUpazilas();

        foreach ($upazilas as $upazila) {
            if ($upazila['id'] === $id) {
                return $upazila;
            }
        }

        return null;
    }

    /**
     * Get all unions
     *
     * @return array<int, array>
     */
    public function getAllUnions(): array
    {
        $unions = [];

        foreach ($this->data['divisions'] ?? [] as $division) {
            foreach ($division['districts'] ?? [] as $district) {
                foreach ($district['upazilas'] ?? [] as $upazila) {
                    foreach ($upazila['unions'] ?? [] as $union) {
                        $unions[] = array_merge($union, [
                            'upazila_id' => $upazila['id'],
                            'upazila_name' => $upazila['name'],
                            'district_id' => $district['id'],
                            'district_name' => $district['name'],
                            'division_id' => $division['id'],
                            'division_name' => $division['name'],
                        ]);
                    }
                }
            }
        }

        return $unions;
    }

    /**
     * Get unions by upazila ID
     *
     * @return array<int, array>
     */
    public function getUnionsByUpazila(string $upazilaId): array
    {
        $upazila = $this->getUpazilaById($upazilaId);

        if (!$upazila) {
            return [];
        }

        return $upazila['unions'] ?? [];
    }

    /**
     * Get union by ID
     */
    public function getUnionById(string $id): ?array
    {
        $unions = $this->getAllUnions();

        foreach ($unions as $union) {
            if ($union['id'] === $id) {
                return $union;
            }
        }

        return null;
    }

    /**
     * Search by name (English or Bengali)
     *
     * @return array<int, array>
     */
    public function searchByName(string $term): array
    {
        // Sanitize search term
        $term = trim($term);
        $term = substr($term, 0, 100); // Limit to 100 characters

        if (empty($term)) {
            return [];
        }

        $results = [];
        $termLower = strtolower($term);

        // Search in divisions
        foreach ($this->getAllDivisions() as $division) {
            if (str_contains(strtolower($division['name']), $termLower) ||
                str_contains($division['name_bn'], $term)) {
                $results[] = array_merge($division, ['type' => 'division']);
            }
        }

        // Search in districts
        foreach ($this->getAllDistricts() as $district) {
            if (str_contains(strtolower($district['name']), $termLower) ||
                str_contains($district['name_bn'], $term)) {
                $results[] = array_merge($district, ['type' => 'district']);
            }
        }

        // Search in upazilas
        foreach ($this->getAllUpazilas() as $upazila) {
            if (str_contains(strtolower($upazila['name']), $termLower) ||
                str_contains($upazila['name_bn'], $term)) {
                $results[] = array_merge($upazila, ['type' => 'upazila']);
            }
        }

        // Search in unions
        foreach ($this->getAllUnions() as $union) {
            if (str_contains(strtolower($union['name']), $termLower) ||
                str_contains($union['name_bn'], $term)) {
                $results[] = array_merge($union, ['type' => 'union']);
            }
        }

        return $results;
    }

    /**
     * Get complete hierarchy for a location
     */
    public function getGeoHierarchy(string $id, string $type): ?array
    {
        $item = null;
        $hierarchy = [];

        switch ($type) {
            case 'division':
                $item = $this->getDivisionById($id);
                if ($item) {
                    $hierarchy = [
                        'division' => $item,
                    ];
                }
                break;

            case 'district':
                $item = $this->getDistrictById($id);
                if ($item) {
                    $division = $this->getDivisionById($item['division_id'] ?? '');
                    $hierarchy = [
                        'division' => $division,
                        'district' => $item,
                    ];
                }
                break;

            case 'upazila':
                $item = $this->getUpazilaById($id);
                if ($item) {
                    $district = $this->getDistrictById($item['district_id'] ?? '');
                    $division = $district ? $this->getDivisionById($district['division_id'] ?? '') : null;
                    $hierarchy = [
                        'division' => $division,
                        'district' => $district,
                        'upazila' => $item,
                    ];
                }
                break;

            case 'union':
                $item = $this->getUnionById($id);
                if ($item) {
                    $upazila = $this->getUpazilaById($item['upazila_id'] ?? '');
                    $district = $upazila ? $this->getDistrictById($upazila['district_id'] ?? '') : null;
                    $division = $district ? $this->getDivisionById($district['division_id'] ?? '') : null;
                    $hierarchy = [
                        'division' => $division,
                        'district' => $district,
                        'upazila' => $upazila,
                        'union' => $item,
                    ];
                }
                break;
        }

        return empty($hierarchy) ? null : $hierarchy;
    }

    /**
     * Clear the geo data cache
     */
    public function clearCache(): void
    {
        Cache::forget('bd-geo-location-data');
    }
}
