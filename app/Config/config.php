<?php

use App\Core\Core;

return [
    'db' => [
        'type' => Core::env('DB_TYPE', 'sqlite'),
        'sqlite' => [
            'file' => __DIR__ . '/../../' . Core::env('DB_SQLITE_FILE', 'database.sqlite')
        ],
        'mysql' => [
            'host' => Core::env('DB_HOST', '127.0.0.1'),
            'port' => (int)Core::env('DB_PORT', 3306),
            'dbname' => Core::env('DB_NAME', 'speedtest'),
            'username' => Core::env('DB_USER', ''),
            'password' => Core::env('DB_PASS', '')
        ],
        'postgresql' => [
            'host' => Core::env('DB_HOST', '127.0.0.1'),
            'port' => (int)Core::env('DB_PORT', 5432),
            'dbname' => Core::env('DB_NAME', 'speedtest'),
            'username' => Core::env('DB_USER', ''),
            'password' => Core::env('DB_PASS', '')
        ],
        'mssql' => [
            'host' => Core::env('DB_HOST', '127.0.0.1'),
            'port' => (int)Core::env('DB_PORT', 1433),
            'dbname' => Core::env('DB_NAME', 'speedtest'),
            'username' => Core::env('DB_USER', ''),
            'password' => Core::env('DB_PASS', ''),
            'win_auth' => filter_var(Core::env('DB_MSSQL_WINDOWS_AUTH', false), FILTER_VALIDATE_BOOLEAN),
            'trust_cert' => filter_var(Core::env('DB_MSSQL_TRUST_CERT', true), FILTER_VALIDATE_BOOLEAN)
        ]
    ],
    'app' => [
        'use_new_design' => filter_var(Core::env('USE_NEW_DESIGN', true), FILTER_VALIDATE_BOOLEAN),
        'title' => Core::env('TITLE', 'LibreSpeed MVC'),
        'tagline' => Core::env('TAGLINE', 'HTML5 Network Speed Test'),
        'admin_email' => Core::env('SPEEDTEST_EMAIL', '')
    ],
    'telemetry' => [
        'password' => Core::env('SPEEDTEST_PASSWORD', ''),
        'enable_id_obfuscation' => filter_var(Core::env('TELEMETRY_OBFUSCATION', true), FILTER_VALIDATE_BOOLEAN),
        'redact_ip_addresses' => filter_var(Core::env('TELEMETRY_REDACT_IP', false), FILTER_VALIDATE_BOOLEAN)
    ],
    'ipinfo' => [
        'apikey' => Core::env('IPINFO_APIKEY', ''),
        'offline_db' => __DIR__ . '/' . basename(Core::env('IPINFO_OFFLINE_DB', 'country_asn.mmdb'))
    ]
];
