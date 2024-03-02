<?php
// Número total de volúmenes esperados
$totalVolumes = 50; // Ajusta este número según la cantidad de volúmenes que planeas tener

// Genera las configuraciones de los discos de volúmenes dinámicamente
$volumeDisksConfig = [];
for ($i = 1; $i <= $totalVolumes; $i++) {
    $volumeName = sprintf('volume-ams3-%02d', $i);
    $volumePath = "/mnt/$volumeName";

    if (file_exists($volumePath)) {
        $volumeDisksConfig[$volumeName] = [
            'driver' => 'local',
            'root' => $volumePath,
            'url' => env('APP_URL') . "/storage/$volumeName",
            'visibility' => 'public',
            'throw' => false,
        ];
    }
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

    ], $volumeDisksConfig), // Combina los discos definidos estáticamente con los dinámicos

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
