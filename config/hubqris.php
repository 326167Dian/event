<?php

return [
    'base_url' => env('HUBQRIS_BASE_URL', 'https://mysifacenter.my.id'),
    'branch_code' => env('HUBQRIS_BRANCH_CODE', 'EVE'),
    'api_key' => env('HUBQRIS_API_KEY'),
    'qr_expiry_minutes' => (int) env('HUBQRIS_QR_EXPIRY_MINUTES', 15),
];
