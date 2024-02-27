<?php
//archivo filesystems.php
// Define los volúmenes dinámicamente aquí. Podrías obtener estos desde una variable de entorno o configuración.
$volumes = [
    'volume-ams3-08' => '/mnt/volume_ams3_08',
    'volume-ams3-09' => '/mnt/volume_ams3_09'
];

// Construye dinámicamente los discos de volúmenes
$volumeDisks = [];
foreach ($volumes as $name => $path) {
    $volumeDisks[$name] = [
        'driver' => 'local',
        'root' => $path,
        'url' => env('APP_URL') . "/storage",
        'visibility' => 'public',
        'throw' => false,
    ];
}

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => array_merge([

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
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
            'throw' => false,
        ],

    ], $volumeDisks), // Combina los discos definidos estáticamente con los dinámicos

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
