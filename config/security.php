<?php

return [
    'token' => [
        'algorithm' => 'HS256',
        'secret' => env('JWT_SECRET'),
        'expiration' => 3600
    ]
];
