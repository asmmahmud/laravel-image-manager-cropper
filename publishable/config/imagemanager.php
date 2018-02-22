<?php

return [
    'admin_url_prefix' => 'admin',
    'admin_middleware' => 'admin.auth',
    'base_admin_url' => '/admin',
    'assets_path' => '/vendor/tasmnaguib/imagemanager/assets',
    'storage' => [
        'disk' => 'public',
    ],
    'quality' => 45,
    'thumbnail_size' => '250',
];
