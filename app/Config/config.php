<?php

return [
    'db' => [
        'type' => env('DB_TYPE', 'sqlite'),
        'sqlite' => [
            'file' => __DIR__ . '/../../' . env('DB_SQLITE_FILE', 'database.sqlite')
        ],
        'mysql' => [
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => (int)env('DB_PORT', 3306),
            'dbname' => env('DB_NAME', 'speedtest'),
            'username' => env('DB_USER', ''),
            'password' => env('DB_PASS', '')
        ],
        'postgresql' => [
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => (int)env('DB_PORT', 5432),
            'dbname' => env('DB_NAME', 'speedtest'),
            'username' => env('DB_USER', ''),
            'password' => env('DB_PASS', '')
        ],
        'mssql' => [
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => (int)env('DB_PORT', 1433),
            'dbname' => env('DB_NAME', 'speedtest'),
            'username' => env('DB_USER', ''),
            'password' => env('DB_PASS', ''),
            'win_auth' => filter_var(env('DB_MSSQL_WINDOWS_AUTH', false), FILTER_VALIDATE_BOOLEAN),
            'trust_cert' => filter_var(env('DB_MSSQL_TRUST_CERT', true), FILTER_VALIDATE_BOOLEAN)
        ]
    ],
    'app' => [
        'use_new_design' => filter_var(env('USE_NEW_DESIGN', true), FILTER_VALIDATE_BOOLEAN),
        'title' => env('TITLE', 'LibreSpeed MVC'),
        'tagline' => env('TAGLINE', 'HTML5 Network Speed Test'),
        'admin_email' => env('SPEEDTEST_EMAIL', '')
    ],
    'telemetry' => [
        'password' => env('SPEEDTEST_PASSWORD', ''),
        'enable_id_obfuscation' => filter_var(env('TELEMETRY_OBFUSCATION', true), FILTER_VALIDATE_BOOLEAN),
        'redact_ip_addresses' => filter_var(env('TELEMETRY_REDACT_IP', false), FILTER_VALIDATE_BOOLEAN)
    ],
    'ipinfo' => [
        'apikey' => env('IPINFO_APIKEY', ''),
        'offline_db' => __DIR__ . '/' . basename(env('IPINFO_OFFLINE_DB', 'country_asn.mmdb'))
    ]
];
