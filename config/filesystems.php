<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
          'Storage_public_path' => [
            'driver' => 'local',
            'root' => storage_path('app/public/'),
            'url'  => env('APP_URL').'/public/storage/',
        ],
        
        'SYSTEM_IMAGE_URL' => [
            'driver' => 'local',
            'url' => env('APP_URL').'/public/storage/images/setting',
            'root'   => storage_path('app/public/images/setting'),
        ],
        'COMMON_IMAGES' => [
            'driver' => 'local',
            'url' => env('APP_URL').'/public/storage/images',
            'root'   => storage_path('app/public/images'),
        ],

        'PRESCRIPTION' => [
            'driver' => 'local',
            'root' => storage_path('app/public/prescription'),
            'url'  => env('APP_URL').'/public/storage/prescription',
        ],
        'PROFILE_PIC' => [
            'driver' => 'local',
            'root' => storage_path('app/public/profile_pic'),
            'url'  => env('APP_URL').'/public/storage/profile_pic',
        ],
        'PROFILE_THUMB' => [
            'driver' => 'local',
            'root' => storage_path('app/public/profile_pic/thumbnails'),
            'url'  => env('APP_URL').'/public/storage/profile_pic/thumbnails',
        ],

        'BRAND_PIC' => [
            'driver' => 'local',
            'root' => storage_path('app/public/brand'),
            'url'  => env('APP_URL').'/public/storage/brand',
        ],
        'BRAND_THUMB' => [
            'driver' => 'local',
            'root' => storage_path('app/public/brand/thumbnails'),
            'url'  => env('APP_URL').'/public/storage/brand/thumbnails',
        ],

        'MEDICINE_PIC' => [
            'driver' => 'local',
            'root' => storage_path('app/public/medicine'),
            'url'  => env('APP_URL').'/public/storage/medicine',
        ],
        'MEDICINE_THUMB' => [
            'driver' => 'local',
            'root' => storage_path('app/public/medicine/thumbnails'),
            'url'  => env('APP_URL').'/public/storage/medicine/thumbnails',
        ],


        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
