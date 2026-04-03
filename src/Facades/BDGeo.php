<?php

namespace Mazhar\BDGeoLocation\Facades;

use Illuminate\Support\Facades\Facade;
use Mazhar\BDGeoLocation\Services\GeoService;

/**
 * @method static array getAllDivisions()
 * @method static array|null getDivisionById(string $id)
 * @method static array getDistrictsByDivision(string $divisionId)
 * @method static array|null getDistrictById(string $id)
 * @method static array getUpazilasByDistrict(string $districtId)
 * @method static array|null getUpazilaById(string $id)
 * @method static array getUnionsByUpazila(string $upazilaId)
 * @method static array|null getUnionById(string $id)
 * @method static array searchByName(string $term)
 * @method static array|null getGeoHierarchy(string $id, string $type)
 * @method static array getAllDistricts()
 * @method static array getAllUpazilas()
 * @method static array getAllUnions()
 * @method static void clearCache()
 * @method static array getStatistics()
 *
 * @see GeoService
 */
class BDGeo extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'bd-geo';
    }
}
