# Version Support Update Summary

**Date**: 2025-04-04
**Package**: mazhar/bd-geo-location

---

## Changes Made

### ✅ Updated PHP Version Support

| Before | After |
|--------|-------|
| PHP 8.1, 8.2, 8.3 | **PHP 8.2, 8.3, 8.4** |

**Rationale**:
- PHP 8.1 reached end of active support (security-only until Dec 2025)
- PHP 8.2, 8.3, 8.4 are actively supported
- PHP 8.5 support will be added when released

### ✅ Updated Laravel Version Support

| Before | After |
|--------|-------|
| Laravel 10, 11 | **Laravel 10, 11, 12** |

**Rationale**:
- Laravel 12 support added for future releases
- Maintains backward compatibility with Laravel 10 and 11
- Ready for Laravel 12 when released

### ✅ Updated Orchestra Testbench

| Before | After |
|--------|-------|
| ^8.0|^9.0 | **^8.0|^9.0|^10.0** |

**Rationale**:
- Testbench 10.x is required for Laravel 12 testing
- Maintains compatibility with older versions

### ✅ Updated PHPUnit

| Before | After |
|--------|-------|
| ^10.0 | **^10.0|^11.0** |

**Rationale**:
- PHPUnit 11 support added
- Maintains backward compatibility with PHPUnit 10

---

## Files Updated

| File | Changes |
|------|---------|
| `composer.json` | Updated PHP requirement to ^8.2, added Laravel 12 support, added Testbench 10.x |
| `.github/workflows/tests.yml` | Updated test matrix to PHP 8.2-8.4 with Laravel 10-12 |
| `README.md` | Added requirements section, updated badges, updated CI/CD info |

---

## Test Matrix

### GitHub Actions CI/CD

The package now tests on **9 combinations**:

| PHP 8.2 | PHP 8.3 | PHP 8.4 |
|---------|---------|---------|
| Laravel 10 | Laravel 10 | Laravel 10 |
| Laravel 11 | Laravel 11 | Laravel 11 |
| Laravel 12 | Laravel 12 | Laravel 12 |

**Total**: 9 test jobs per run

---

## New Badges Added to README

```markdown
[![PHP Version](https://img.shields.io/packagist/php-v/mazhar/bd-geo-location?style=flat-square)]
[![Laravel](https://img.shields.io/badge/laravel-10%20%7C%2011%20%7C%2012-FF2D20?style=flat-square)]
```

---

## Compatibility Matrix

| Laravel Version | PHP 8.2 | PHP 8.3 | PHP 8.4 | Status |
|-----------------|---------|---------|---------|--------|
| Laravel 10.x | ✅ | ✅ | ✅ | Supported |
| Laravel 11.x | ✅ | ✅ | ✅ | Supported |
| Laravel 12.x | ✅ | ✅ | ✅ | Supported (when released) |

---

## Breaking Changes

⚠️ **PHP 8.1 No Longer Supported**

If you're still using PHP 8.1, you must:
1. Upgrade to PHP 8.2 or higher
2. Use version 1.0.0 of this package (last version with PHP 8.1 support)

**Migration**: No code changes required - just upgrade PHP version.

---

## PHP Version Support Timeline

| PHP Version | Active Support Until | Security Fixes Until | Status in Package |
|-------------|---------------------|---------------------|-------------------|
| PHP 8.1 | Dec 31, 2024 | Dec 31, 2025 | ❌ Dropped |
| PHP 8.2 | Dec 8, 2025 | Dec 31, 2028 | ✅ Supported |
| PHP 8.3 | Dec 31, 2026 | Dec 31, 2029 | ✅ Supported |
| PHP 8.4 | Dec 31, 2027 | Dec 31, 2030 | ✅ Supported |
| PHP 8.5 | TBD | TBD | 🔄 Will add when released |

---

## Laravel Version Support

| Laravel Version | Release Date | PHP Requirement | Status |
|-----------------|--------------|-----------------|--------|
| Laravel 10.x | Feb 2023 | PHP 8.1+ | ✅ Supported |
| Laravel 11.x | Mar 2024 | PHP 8.2+ | ✅ Supported |
| Laravel 12.x | Q1 2025 (expected) | PHP 8.2+ | ✅ Ready |

---

## Testing Commands

### Local Testing

```bash
# Run tests on current PHP version
composer test

# Run tests with coverage
composer test:coverage

# Run full CI pipeline
composer ci
```

### Expected Test Results

All tests should pass on:
- ✅ PHP 8.2 with Laravel 10, 11, 12
- ✅ PHP 8.3 with Laravel 10, 11, 12
- ✅ PHP 8.4 with Laravel 10, 11, 12

---

## Migration Guide for Users

### If You're Using PHP 8.1

**Option 1**: Upgrade PHP (Recommended)
```bash
# Upgrade to PHP 8.2 or higher
# Then update the package
composer update mazhar/bd-geo-location
```

**Option 2**: Stay on Old Version
```bash
# Keep using v1.0.0 with PHP 8.1 support
# Lock the version in composer.json:
"mazhar/bd-geo-location": "^1.0.0"
```

### If You're Already on PHP 8.2+

No changes needed! Just update:
```bash
composer update mazhar/bd-geo-location
```

---

## Next Steps

1. **Commit and push** these changes to GitHub
2. **Create a release** with updated version notes
3. **Update Packagist** - it will automatically detect the new requirements
4. **Communicate breaking change** (PHP 8.1 dropped) to users

---

## Summary

✅ **PHP Support**: Now requires PHP 8.2+
✅ **Laravel Support**: Added Laravel 12 support
✅ **CI/CD**: Tests on all modern PHP and Laravel versions
✅ **Backward Compatibility**: Maintained for Laravel 10 and 11
⚠️ **Breaking Change**: PHP 8.1 no longer supported

The package is now ready for the latest and upcoming versions of PHP and Laravel! 🚀
