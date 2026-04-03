# BD Geo Location for Laravel

A comprehensive Bangladesh Geo Location data package for Laravel with support for Divisions, Districts, Upazilas, and Unions.

[![Latest Version](https://img.shields.io/packagist/v/mazhar/bd-geo-location?style=flat-square)](https://packagist.org/packages/mazhar/bd-geo-location)
[![Total Downloads](https://img.shields.io/packagist/dt/mazhar/bd-geo-location?style=flat-square)](https://packagist.org/packages/mazhar/bd-geo-location)
[![License](https://img.shields.io/packagist/l/mazhar/bd-geo-location?style=flat-square)](https://packagist.org/packages/mazhar/bd-geo-location)
[![PHP Version](https://img.shields.io/packagist/php-v/mazhar/bd-geo-location?style=flat-square)](https://packagist.org/packages/mazhar/bd-geo-location)
[![Laravel](https://img.shields.io/badge/laravel-10%20%7C%2011%20%7C%2012-FF2D20?style=flat-square)](https://laravel.com)
[![Tests](https://github.com/mazharvai007/bd-geo-location-laravel/actions/workflows/tests.yml/badge.svg)](https://github.com/mazharvai007/bd-geo-location-laravel/actions/workflows/tests.yml)
[![codecov](https://codecov.io/gh/mazharvai007/bd-geo-location-laravel/branch/main/graph/badge.svg)](https://codecov.io/gh/mazharvai007/bd-geo-location-laravel)

## Features

- Complete administrative hierarchy of Bangladesh
  - 8 Divisions
  - 68 Districts
  - 531 Upazilas
  - 4,916 Unions
- Bilingual (English & Bengali) names
- JSON-based data (fast, zero-config)
- Optional database support
- Search functionality
- Validation rules
- Artisan commands
- Helper functions
- Laravel Facade support

## Requirements

- **PHP**: 8.2, 8.3, 8.4
- **Laravel**: 10, 11, 12
- **Composer**: 2.x

## Installation

Install the package via Composer:

```bash
composer require mazhar/bd-geo-location
```

The package will auto-register the service provider and facade in Laravel.

## Configuration (Optional)

Publish the configuration file:

```bash
php artisan vendor:publish --tag=bd-geo-config
```

This will create `config/bd-geo.php` where you can configure:

```php
return [
    // Data source: 'json' (default) or 'database'
    'data_source' => env('BD_GEO_DATA_SOURCE', 'json'),

    // Cache duration in seconds (default: 7 days)
    'cache_duration' => env('BD_GEO_CACHE_DURATION', 604800),

    // Database table prefix
    'table_prefix' => env('BD_GEO_TABLE_PREFIX', 'bd_'),
];
```

## Usage

### Using Facade

```php
use Mazhar\BDGeoLocation\Facades\BDGeo;

// Get all divisions
$divisions = BDGeo::getAllDivisions();

// Get division by ID
$division = BDGeo::getDivisionById('30');

// Get districts by division
$districts = BDGeo::getDistrictsByDivision('30');

// Get upazilas by district
$upazilas = BDGeo::getUpazilasByDistrict('3034');

// Get unions by upazila
$unions = BDGeo::getUnionsByUpazila('303427');

// Search by name
$results = BDGeo::searchByName('Dhaka');

// Get complete hierarchy
$hierarchy = BDGeo::getGeoHierarchy('303427', 'upazila');
```

### Using Helper Functions

```php
// Get all divisions
$divisions = bd_divisions();

// Get division by ID
$division = bd_division('30');

// Get districts (all or by division)
$districts = bd_districts('30');

// Get upazilas (all or by district)
$upazilas = bd_upazilas('3034');

// Get unions (all or by upazila)
$unions = bd_unions('303427');

// Search locations
$results = bd_geo_search('Dhaka');

// Get hierarchy
$hierarchy = bd_geo_hierarchy('303427', 'upazila');
```

### In Controllers

```php
use Mazhar\BDGeoLocation\Facades\BDGeo;

class LocationController extends Controller
{
    public function index()
    {
        $divisions = BDGeo::getAllDivisions();

        return response()->json($divisions);
    }

    public function districts($divisionId)
    {
        $districts = BDGeo::getDistrictsByDivision($divisionId);

        return response()->json($districts);
    }

    public function search(Request $request)
    {
        $results = BDGeo::searchByName($request->query('q'));

        return response()->json($results);
    }
}
```

### In Blade Templates

```blade
<select name="division">
    @foreach(bd_divisions() as $division)
        <option value="{{ $division['id'] }}">
            {{ $division['name'] }} ({{ $division['name_bn'] }})
        </option>
    @endforeach
</select>
```

## Validation Rules

Use the built-in validation rules to validate geo location IDs:

```php
use Illuminate\Http\Request;
use Mazhar\BDGeoLocation\Validation\Rules\BDDivision;
use Mazhar\BDGeoLocation\Validation\Rules\BDDistrict;
use Mazhar\BDGeoLocation\Validation\Rules\BDUpazila;
use Mazhar\BDGeoLocation\Validation\Rules\BDUnion;

public function store(Request $request)
{
    $validated = $request->validate([
        'division_id' => ['required', new BDDivision],
        'district_id' => ['required', new BDDistrict],
        'upazila_id' => ['nullable', new BDUpazila],
        'union_id' => ['nullable', new BDUnion],
    ]);

    // Your logic here
}
```

## Artisan Commands

### Seed Geo Data to Database

If you want to use database instead of JSON, first publish and run migrations:

```bash
# Publish migrations
php artisan vendor:publish --tag=bd-geo-migrations

# Run migrations
php artisan migrate

# Seed data to database
php artisan bd-geo:seed
```

### Cache Management

```bash
# Clear cached geo data
php artisan bd-geo:cache --clear

# Warm up cache
php artisan bd-geo:cache --warm
```

## Database Support (Optional)

By default, the package uses JSON files for fast access. If you prefer database queries:

1. Set `BD_GEO_DATA_SOURCE=database` in your `.env` file
2. Publish and run migrations
3. Seed the data

```bash
php artisan vendor:publish --tag=bd-geo-migrations
php artisan migrate
php artisan bd-geo:seed
```

Now you can also use Eloquent models:

```php
use Mazhar\BDGeoLocation\Models\Division;
use Mazhar\BDGeoLocation\Models\District;
use Mazhar\BDGeoLocation\Models\Upazila;
use Mazhar\BDGeoLocation\Models\Union;

// Get division with districts
$division = Division::with('districts')->find('30');

// Get district with upazilas
$district = District::with('upazilas')->find('3034');

// Query with relationships
$upazilas = Upazila::with('district.division')->get();
```

## Data Structure

Each location entity contains:

```php
[
    'id' => '30',           // Unique ID
    'name' => 'Dhaka',      // English name
    'name_bn' => 'ঢাকা',   // Bengali name
    'lat' => 23.7104,       // Latitude (divisions & districts)
    'long' => 90.4074,      // Longitude (divisions & districts)
]
```

## API Response Example

```json
{
    "divisions": [
        {
            "id": "30",
            "name": "Dhaka",
            "name_bn": "ঢাকা",
            "lat": "23.71040000",
            "long": "90.40740000"
        }
    ],
    "districts": [
        {
            "id": "3034",
            "division_id": "30",
            "name": "Dhaka",
            "name_bn": "ঢাকা",
            "lat": "23.71153000",
            "long": "90.41158000"
        }
    ]
}
```

## Testing

```bash
# Run tests
composer test

# Run tests with coverage report
composer test:coverage

# Run full CI pipeline locally
composer ci
```

## Development

### Code Quality Checks

This package includes automated code quality checks:

```bash
# Run static analysis (PHPStan)
composer analyse

# Validate composer.json
composer validate --strict

# Run all quality checks
composer quality
```

### PHPStan Configuration

The package uses [Larastan](https://github.com/larastan/larastan) for static analysis at level 5. Configuration is in `phpstan.neon`.

### CI/CD

This package uses GitHub Actions for continuous integration:

- ✅ Tests on PHP 8.2, 8.3, 8.4 with Laravel 10, 11, and 12
- ✅ Static analysis with PHPStan
- ✅ Code quality checks
- ✅ Security audits
- ✅ Code coverage reporting via Codecov

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Credits

- [mazharvai](https://github.com/mazharvai)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

## Also Available

- [JavaScript/TypeScript version (npm)](https://www.npmjs.com/package/bd-geo-location) - For React, Vue, Angular, Node.js
- [React Native version](https://www.npmjs.com/package/bd-geo-location) - For mobile apps

## Support

For issues, questions, or contributions, please visit [GitHub Issues](https://github.com/mazharvai/bd-geo-location-laravel/issues).
