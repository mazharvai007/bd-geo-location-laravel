<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Data Source
    |--------------------------------------------------------------------------
    |
    | Configure where to load the geo data from.
    | Options: 'json' (default, fast) or 'database' (requires seeding)
    |
    */
    'data_source' => env('BD_GEO_DATA_SOURCE', 'json'),

    /*
    |--------------------------------------------------------------------------
    | Cache Duration
    |--------------------------------------------------------------------------
    |
    | How long to cache the geo data in seconds.
    | Default: 604800 (7 days)
    | Set to 0 to disable caching.
    |
    */
    'cache_duration' => env('BD_GEO_CACHE_DURATION', 604800),

    /*
    |--------------------------------------------------------------------------
    | Search Limit
    |--------------------------------------------------------------------------
    |
    | Maximum number of results to return from search operations.
    | Default: 100
    | Set to 0 for unlimited (not recommended for performance).
    |
    */
    'search_limit' => env('BD_GEO_SEARCH_LIMIT', 100),

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | The database connection to use when data_source is set to 'database'.
    | Leave null to use the default connection.
    |
    */
    'database_connection' => env('BD_GEO_DB_CONNECTION', null),

    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix for database tables when using database mode.
    |
    */
    'table_prefix' => env('BD_GEO_TABLE_PREFIX', 'bd_'),

    /*
    |--------------------------------------------------------------------------
    | API Response Format
    |--------------------------------------------------------------------------
    |
    | Default format for API responses.
    | Options: 'array', 'collection'
    |
    */
    'api_format' => env('BD_GEO_API_FORMAT', 'array'),

];
