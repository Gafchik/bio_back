<?php
return [
    'stripe' => [
        'token' => env('STRIPE_SECRET_TOKEN'),
        'baseSuccessUrl' => env('STRIPE_BASE_SUCCESS_URL','https://bio-front.dichajeka1.online/personal'),
        'ttl' => env('STRIPE_TTL',1850), //stripe min ttl 30 minute
    ],
];
