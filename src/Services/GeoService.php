<?php

namespace Mazhar\BDGeoLocation\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use InvalidArgumentException;

class GeoService
{
    protected array $data = [];

    protected string $dataSource;

    protected int $cacheDuration;

    protected ?array $divisionsCache = null;

    protected ?array $districtsCache = null;

    protected ?array $upazilasCache = null;

    protected ?array $unionsCache = null;

    protected const ALLOWED_TYPES = ['division', 'district', 'upazila', 'union'];

    public function __construct()
    {
        $this->dataSource = config('bd-geo.data_source', 'json');
        $this->cacheDuration = (int) config('bd-geo.cache_duration', 604800);

        $this->loadData();
    }

    /**
     * Load geo data based on configuration
     */
    protected function loadData(): void
    {
        try {
            if ($this->dataSource === 'database') {
                $this->loadFromDatabase();
            } else {
                $this->loadFromJson();
            }
        } catch (\Exception $e) {
            report($e);
            $this->data = [];
        }
    }

    /**
     * Load data from JSON file with error handling
     */
    protected function loadFromJson(): void
    {
        $cacheKey = 'bd-geo-location-data';

        $this->data = Cache::remember($cacheKey, $this->cacheDuration, function () {
            $jsonPath = $this->getDataFilePath();

            if (!file_exists($jsonPath)) {
                report(new \RuntimeException("Geo data file not found: {$jsonPath}"));
                return [];
            }

            if (!is_readable($jsonPath)) {
                report(new \RuntimeException("Geo data file is not readable: {$jsonPath}"));
                return [];
            }

            $jsonContent = file_get_contents($jsonPath);

            if ($jsonContent === false) {
                report(new \RuntimeException("Failed to read geo data file: {$jsonPath}"));
                return [];
            }

            $data = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($data)) {
                report(new \RuntimeException("Invalid geo data format"));
                return [];
            }

            return $data;
        });
    }

    /**
     * Get cross-platform data file path
     */
    protected function getDataFilePath(): string
    {
        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'bangladesh.json';

        return $path;
    }

    /**
     * Load data from database
     */
    protected function loadFromDatabase(): void
    {
        $this->loadFromJson();
    }

    /**
     * Validate location ID format
     */
    protected function validateId(string $id): void
    {
        if (empty($id) || !is_string($id)) {
            throw new InvalidArgumentException('Invalid location ID provided');
        }
    }

    /**
     * Validate location type
     */
    protected function validateType(string $type): void
    {
        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid location type "%s". Must be one of: %s', $type, implode(', ', self::ALLOWED_TYPES))
            );
        }
    }

    /**
     * Get all divisions with caching
     *
     * @return array<int, array>
     */
    public function getAllDivisions(): array
    {
        if ($this->divisionsCache === null) {
            $this->divisionsCache = $this->data['divisions'] ?? [];
        }

        return $this->divisionsCache;
    }

    /**
     * Get division by ID
     */
    public function getDivisionById(string $id): ?array
    {
        $this->validateId($id);

        $divisions = $this->getAllDivisions();

        foreach ($divisions as $division) {
            if (isset($division['id']) && (string) $division['id'] === $id) {
                return $division;
            }
        }

        return null;
    }

    /**
     * Get all districts with caching
     *
     * @return array<int, array>
     */
    public function getAllDistricts(): array
    {
        if ($this->districtsCache === null) {
            $districts = [];

            foreach ($this->getAllDivisions() as $division) {
                $divisionData = $division ?? [];
                $divisionId = $divisionData['id'] ?? '';
                $divisionName = $divisionData['name'] ?? '';
                $divisionNameBn = $divisionData['name_bn'] ?? '';

                foreach ($divisionData['districts'] ?? [] as $district) {
                    $districts[] = array_merge($district, [
                        'division_id' => $divisionId,
                        'division_name' => $divisionName,
                        'division_name_bn' => $divisionNameBn,
                    ]);
                }
            }

            $this->districtsCache = $districts;
        }

        return $this->districtsCache;
    }

    /**
     * Get districts by division ID
     *
     * @return array<int, array>
     */
    public function getDistrictsByDivision(string $divisionId): array
    {
        $this->validateId($divisionId);

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
        $this->validateId($id);

        $districts = $this->getAllDistricts();

        foreach ($districts as $district) {
            if (isset($district['id']) && (string) $district['id'] === $id) {
                return $district;
            }
        }

        return null;
    }

    /**
     * Get all upazilas with caching
     *
     * @return array<int, array>
     */
    public function getAllUpazilas(): array
    {
        if ($this->upazilasCache === null) {
            $upazilas = [];

            foreach ($this->getAllDivisions() as $division) {
                $divisionData = $division ?? [];
                $divisionId = $divisionData['id'] ?? '';
                $divisionName = $divisionData['name'] ?? '';

                foreach ($divisionData['districts'] ?? [] as $district) {
                    $districtData = $district ?? [];
                    $districtId = $districtData['id'] ?? '';
                    $districtName = $districtData['name'] ?? '';

                    foreach ($districtData['upazilas'] ?? [] as $upazila) {
                        $upazilas[] = array_merge($upazila, [
                            'district_id' => $districtId,
                            'district_name' => $districtName,
                            'division_id' => $divisionId,
                            'division_name' => $divisionName,
                        ]);
                    }
                }
            }

            $this->upazilasCache = $upazilas;
        }

        return $this->upazilasCache;
    }

    /**
     * Get upazilas by district ID
     *
     * @return array<int, array>
     */
    public function getUpazilasByDistrict(string $districtId): array
    {
        $this->validateId($districtId);

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
        $this->validateId($id);

        $upazilas = $this->getAllUpazilas();

        foreach ($upazilas as $upazila) {
            if (isset($upazila['id']) && (string) $upazila['id'] === $id) {
                return $upazila;
            }
        }

        return null;
    }

    /**
     * Get all unions with caching (lazy-loaded for memory efficiency)
     *
     * @return array<int, array>
     */
    public function getAllUnions(): array
    {
        if ($this->unionsCache === null) {
            $unions = [];

            foreach ($this->getAllDivisions() as $division) {
                $divisionData = $division ?? [];
                $divisionId = $divisionData['id'] ?? '';
                $divisionName = $divisionData['name'] ?? '';

                foreach ($divisionData['districts'] ?? [] as $district) {
                    $districtData = $district ?? [];
                    $districtId = $districtData['id'] ?? '';
                    $districtName = $districtData['name'] ?? '';

                    foreach ($districtData['upazilas'] ?? [] as $upazila) {
                        $upazilaData = $upazila ?? [];
                        $upazilaId = $upazilaData['id'] ?? '';
                        $upazilaName = $upazilaData['name'] ?? '';

                        foreach ($upazilaData['unions'] ?? [] as $union) {
                            $unions[] = array_merge($union, [
                                'upazila_id' => $upazilaId,
                                'upazila_name' => $upazilaName,
                                'district_id' => $districtId,
                                'district_name' => $districtName,
                                'division_id' => $divisionId,
                                'division_name' => $divisionName,
                            ]);
                        }
                    }
                }
            }

            $this->unionsCache = $unions;
        }

        return $this->unionsCache;
    }

    /**
     * Get unions by upazila ID
     *
     * @return array<int, array>
     */
    public function getUnionsByUpazila(string $upazilaId): array
    {
        $this->validateId($upazilaId);

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
        $this->validateId($id);

        $unions = $this->getAllUnions();

        foreach ($unions as $union) {
            if (isset($union['id']) && (string) $union['id'] === $id) {
                return $union;
            }
        }

        return null;
    }

    /**
     * Search by name (English or Bengali) with improved performance
     *
     * @return array<int, array>
     */
    public function searchByName(string $term): array
    {
        $term = $this->sanitizeSearchTerm($term);

        if (empty($term)) {
            return [];
        }

        $results = [];
        $termLower = strtolower($term);

        $limit = (int) config('bd-geo.search_limit', 100);

        $searchInCollection = function (array $collection, string $type) use ($termLower, &$results, $limit): void {
            if (count($results) >= $limit) {
                return;
            }

            foreach ($collection as $item) {
                if (count($results) >= $limit) {
                    break;
                }

                $name = $item['name'] ?? '';
                $nameBn = $item['name_bn'] ?? '';

                if ($this->matchesSearchTerm($name, $nameBn, $termLower)) {
                    $results[] = array_merge($item, ['type' => $type]);
                }
            }
        };

        $searchInCollection($this->getAllDivisions(), 'division');
        $searchInCollection($this->getAllDistricts(), 'district');
        $searchInCollection($this->getAllUpazilas(), 'upazila');

        if (count($results) < $limit) {
            $searchInCollection($this->getAllUnions(), 'union');
        }

        return $results;
    }

    /**
     * Sanitize search term
     */
    protected function sanitizeSearchTerm(string $term): string
    {
        $term = trim($term);
        $term = strip_tags($term);
        $term = preg_replace('/[\x00-\x1F\x7F]/u', '', $term);
        $term = mb_substr($term, 0, 100, 'UTF-8');

        return $term;
    }

    /**
     * Check if location matches search term
     */
    protected function matchesSearchTerm(string $name, string $nameBn, string $termLower): bool
    {
        $nameLower = strtolower($name);

        return str_contains($nameLower, $termLower) || str_contains($nameBn, $termLower);
    }

    /**
     * Get complete hierarchy for a location with validation
     */
    public function getGeoHierarchy(string $id, string $type): ?array
    {
        $this->validateId($id);
        $this->validateType($type);

        $hierarchy = [];

        switch ($type) {
            case 'division':
                $item = $this->getDivisionById($id);
                if ($item) {
                    $hierarchy = ['division' => $item];
                }
                break;

            case 'district':
                $item = $this->getDistrictById($id);
                if ($item) {
                    $divisionId = is_array($item) ? ($item['division_id'] ?? '') : '';
                    $division = $this->getDivisionById($divisionId);
                    $hierarchy = [
                        'division' => $division,
                        'district' => $item,
                    ];
                }
                break;

            case 'upazila':
                $item = $this->getUpazilaById($id);
                if ($item) {
                    $districtId = is_array($item) ? ($item['district_id'] ?? '') : '';
                    $district = $this->getDistrictById($districtId);
                    $division = $district ? $this->getDivisionById($district['division_id'] ?? '' ?? '') : null;
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
                    $upazilaId = is_array($item) ? ($item['upazila_id'] ?? '') : '';
                    $upazila = $this->getUpazilaById($upazilaId);
                    $district = $upazila ? $this->getDistrictById($upazila['district_id'] ?? '' ?? '') : null;
                    $division = $district ? $this->getDivisionById($district['division_id'] ?? '' ?? '') : null;
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
     * Clear all caches including computed data
     */
    public function clearCache(): void
    {
        Cache::forget('bd-geo-location-data');

        $this->divisionsCache = null;
        $this->districtsCache = null;
        $this->upazilasCache = null;
        $this->unionsCache = null;
    }

    /**
     * Get statistics about the data
     */
    public function getStatistics(): array
    {
        return [
            'divisions' => count($this->getAllDivisions()),
            'districts' => count($this->getAllDistricts()),
            'upazilas' => count($this->getAllUpazilas()),
            'unions' => count($this->getAllUnions()),
            'data_source' => $this->dataSource,
            'cache_enabled' => $this->cacheDuration > 0,
        ];
    }
}
