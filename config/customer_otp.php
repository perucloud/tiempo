<?php

return [
    'ttl_minutes' => (int) env('CUSTOMER_OTP_TTL', 5),
    'max_attempts' => (int) env('CUSTOMER_OTP_MAX_ATTEMPTS', 5),
    'driver' => env('CUSTOMER_OTP_DRIVER', 'log'),
];
