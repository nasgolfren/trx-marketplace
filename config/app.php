<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store' => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\FortifyServiceProvider::class,
        App\Providers\JetstreamServiceProvider::class,
    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
    ])->toArray(),


    /*
    |--------------------------------------------------------------------------
    | APP CONFIG
    |--------------------------------------------------------------------------
    */
    'formConfig' => [
        'energy' => [
            'minAmountValue' => 64000,
            //'minPriceValue' => 50,
            'durations' => [
                ['name' => '1 hour', 'code' => 1, 'price' => 60,],
                ['name' => '3 hours', 'code' => 3, 'price' => 70,],
                ['name' => '1 day', 'code' => 24, 'price' => 75,],
                ['name' => '2 days', 'code' => 48, 'price' => 75,],
                ['name' => '3 days', 'code' => 72, 'price' => 50,],
                ['name' => '4 days', 'code' => 96, 'price' => 50,],
                ['name' => '5 days', 'code' => 120, 'price' => 50,],
                ['name' => '6 days', 'code' => 144, 'price' => 50,],
                ['name' => '7 days', 'code' => 168, 'price' => 50,],
                ['name' => '8 days', 'code' => 192, 'price' => 50,],
                ['name' => '9 days', 'code' => 216, 'price' => 50,],
                ['name' => '10 days', 'code' => 240, 'price' => 50,],
                ['name' => '11 days', 'code' => 264, 'price' => 50,],
                ['name' => '12 days', 'code' => 288, 'price' => 50,],
                ['name' => '13 days', 'code' => 312, 'price' => 50,],
                ['name' => '14 days', 'code' => 336, 'price' => 50,],
                ['name' => '15 days', 'code' => 360, 'price' => 50,],
                ['name' => '16 days', 'code' => 384, 'price' => 50,],
                ['name' => '17 days', 'code' => 408, 'price' => 50,],
                ['name' => '18 days', 'code' => 432, 'price' => 50,],
                ['name' => '19 days', 'code' => 456, 'price' => 50,],
                ['name' => '20 days', 'code' => 480, 'price' => 50,],
                ['name' => '21 days', 'code' => 504, 'price' => 50,],
                ['name' => '22 days', 'code' => 528, 'price' => 50,],
                ['name' => '23 days', 'code' => 552, 'price' => 50,],
                ['name' => '24 days', 'code' => 576, 'price' => 50,],
                ['name' => '25 days', 'code' => 600, 'price' => 50,],
                ['name' => '26 days', 'code' => 624, 'price' => 50,],
                ['name' => '27 days', 'code' => 648, 'price' => 50,],
                ['name' => '28 days', 'code' => 672, 'price' => 50,],
                ['name' => '29 days', 'code' => 696, 'price' => 50,],
                ['name' => '30 days', 'code' => 720, 'price' => 50,],
            ],
        ],
        'bandwidth' => [
            'minAmountValue' => 2000,
            //'minPriceValue' => 10,
            'durations' => [
                ['name' => '1 hour', 'code' => 1, 'price' => 1500,],
                ['name' => '3 hours', 'code' => 3, 'price' => 1600,],
                ['name' => '1 day', 'code' => 24, 'price' => 1750,],
                ['name' => '2 days', 'code' => 48, 'price' => 1750,],
                ['name' => '3 days', 'code' => 72, 'price' => 1000,],
                ['name' => '4 days', 'code' => 96, 'price' => 1000,],
                ['name' => '5 days', 'code' => 120, 'price' => 1000,],
                ['name' => '6 days', 'code' => 144, 'price' => 1000,],
                ['name' => '7 days', 'code' => 168, 'price' => 1000,],
                ['name' => '8 days', 'code' => 192, 'price' => 1000,],
                ['name' => '9 days', 'code' => 216, 'price' => 1000,],
                ['name' => '10 days', 'code' => 240, 'price' => 1000,],
                ['name' => '11 days', 'code' => 264, 'price' => 1000,],
                ['name' => '12 days', 'code' => 288, 'price' => 1000,],
                ['name' => '13 days', 'code' => 312, 'price' => 1000,],
                ['name' => '14 days', 'code' => 336, 'price' => 1000,],
                ['name' => '15 days', 'code' => 360, 'price' => 1000,],
                ['name' => '16 days', 'code' => 384, 'price' => 1000,],
                ['name' => '17 days', 'code' => 408, 'price' => 1000,],
                ['name' => '18 days', 'code' => 432, 'price' => 1000,],
                ['name' => '19 days', 'code' => 456, 'price' => 1000,],
                ['name' => '20 days', 'code' => 480, 'price' => 1000,],
                ['name' => '21 days', 'code' => 504, 'price' => 1000,],
                ['name' => '22 days', 'code' => 528, 'price' => 1000,],
                ['name' => '23 days', 'code' => 552, 'price' => 1000,],
                ['name' => '24 days', 'code' => 576, 'price' => 1000,],
                ['name' => '25 days', 'code' => 600, 'price' => 1000,],
                ['name' => '26 days', 'code' => 624, 'price' => 1000,],
                ['name' => '27 days', 'code' => 648, 'price' => 1000,],
                ['name' => '28 days', 'code' => 672, 'price' => 1000,],
                ['name' => '29 days', 'code' => 696, 'price' => 1000,],
                ['name' => '30 days', 'code' => 720, 'price' => 1000,],
            ],
        ],
    ],

    'resources' => [
        ['name' => 'Energy', 'code' => 'energy',],
        ['name' => 'Bandwidth', 'code' => 'bandwidth',],
    ],

    'target_address' => 'TFN27Ke45MWyR2gouj5mHXxzeXz9DeAHd6',
];
