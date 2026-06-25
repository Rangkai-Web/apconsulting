<?php

return [
    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),
    'api_key' => env('RAJAONGKIR_API_KEY'),
    'couriers' => array_filter(explode(',', env('RAJAONGKIR_COURIERS', 'jne,jnt,sicepat'))),
    'cache_ttl_hours' => (int) env('RAJAONGKIR_CACHE_TTL_HOURS', 6),
];
