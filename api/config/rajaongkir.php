<?php

return [
    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),
    'api_key' => env('RAJAONGKIR_API_KEY'),
    'couriers' => array_filter(explode(',', env('RAJAONGKIR_COURIERS', 'jnt,pos'))),
    'cache_ttl_hours' => (int) env('RAJAONGKIR_CACHE_TTL_HOURS', 6),

    // Allowlist of retail parcel service codes per courier. Cargo/trucking-tier
    // services (e.g. JNE's JTR family, priced per kg bracket) are excluded by
    // default since they don't apply to single-parcel retail shipments.
    'retail_services' => [
        'jne' => ['REG', 'YES', 'OKE', 'SPS', 'CTC', 'CTCYES', 'CTCSPS'],
        'jnt' => ['EZ', 'REG'],
        'sicepat' => ['REG', 'BEST', 'HALU', 'GOKIL'],
        'pos' => ['Pos Reguler', 'Pos Nextday', 'POSREG', 'POS KILAT KHUSUS', 'Pos Kilat Khusus', 'Pos Sameday', 'Pos Express', 'Paketpos Cepat', 'Surat Kilat Khusus'],
    ],
];
