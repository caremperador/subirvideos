<?php
/* 
$volumes = [];
for ($i = 1; $i <= 50; $i++) {
    $volumeName = sprintf('volume-ams3-%02d', $i);
    $volumes[$volumeName] = '/mnt/' . $volumeName;
}

// Filtra y construye dinámicamente los discos de volúmenes solo si existen.
$volumeDisksConfig = [];
foreach ($volumes as $name => $path) {
    if (file_exists($path)) {
        $volumeDisksConfig[$name] = [
            'driver' => 'local',
            'root' => $path,
            'url' => env('APP_URL') . "/$name", // Ajusta según sea necesario
            'visibility' => 'public',
            'throw' => false,
        ];
    }
}
*/

// Define los volúmenes dinámicamente aquí. Podrías obtener estos desde una variable de entorno o configuración.
$volumes = [
    'volume-ams3-01' => '/mnt/volume_ams3_01',
    'volume-ams3-02' => '/mnt/volume_ams3_02',
    'volume-ams3-03' => '/mnt/volume_ams3_03',
    'volume-ams3-04' => '/mnt/volume_ams3_04',
    'volume-ams3-05' => '/mnt/volume_ams3_05',
    'volume-ams3-06' => '/mnt/volume_ams3_06',
    'volume-ams3-07' => '/mnt/volume_ams3_07',
    'volume-ams3-08' => '/mnt/volume_ams3_08',
    'volume-ams3-09' => '/mnt/volume_ams3_09',
    'volume-ams3-10' => '/mnt/volume_ams3_10',
    'volume-ams3-11' => '/mnt/volume_ams3_11',
    'volume-ams3-12' => '/mnt/volume_ams3_12',
    'volume-ams3-13' => '/mnt/volume_ams3_13',
    'volume-ams3-14' => '/mnt/volume_ams3_14',
    'volume-ams3-15' => '/mnt/volume_ams3_15',
    'volume-ams3-16' => '/mnt/volume_ams3_16',
    'volume-ams3-17' => '/mnt/volume_ams3_17',
    'volume-ams3-18' => '/mnt/volume_ams3_18',
    'volume-ams3-19' => '/mnt/volume_ams3_19',
    'volume-ams3-20' => '/mnt/volume_ams3_20'
];

// Filtra y construye dinámicamente los discos de volúmenes solo si existen.
$volumeDisksConfig = [];
foreach ($volumes as $name => $path) {
    if (file_exists($path)) {
        $volumeDisksConfig[$name] = [
            'driver' => 'local',
            'root' => $path,
            // Aquí ajustas la URL para que apunte al directorio correcto, si es necesario
            'url' => env('APP_URL') . "/$name", // Asegúrate de que esta ruta sea accesible públicamente si es necesario
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
