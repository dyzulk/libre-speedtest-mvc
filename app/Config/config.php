<?php

return [
    'db' => [
        'type' => $_ENV['DB_TYPE'] ?? 'sqlite',
        'sqlite' => [
            'file' => __DIR__ . '/../../' . ($_ENV['DB_SQLITE_FILE'] ?? 'database.sqlite')
        ],
        'mysql' => [
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['DB_PORT'] ?? 3306,
            'dbname' => $_ENV['DB_NAME'] ?? 'speedtest',
            'username' => $_ENV['DB_USER'] ?? '',
            'password' => $_ENV['DB_PASS'] ?? ''
        ],
        'postgresql' => [
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['DB_PORT'] ?? 5432,
            'dbname' => $_ENV['DB_NAME'] ?? 'speedtest',
            'username' => $_ENV['DB_USER'] ?? '',
            'password' => $_ENV['DB_PASS'] ?? ''
        ],
        'mssql' => [
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['DB_PORT'] ?? 1433,
            'dbname' => $_ENV['DB_NAME'] ?? 'speedtest',
            'username' => $_ENV['DB_USER'] ?? '',
            'password' => $_ENV['DB_PASS'] ?? '',
            'win_auth' => filter_var($_ENV['DB_MSSQL_WINDOWS_AUTH'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'trust_cert' => filter_var($_ENV['DB_MSSQL_TRUST_CERT'] ?? true, FILTER_VALIDATE_BOOLEAN)
        ]
    ],
    'app' => [
        'use_new_design' => filter_var($_ENV['USE_NEW_DESIGN'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'title' => $_ENV['TITLE'] ?? 'LibreSpeed MVC',
        'tagline' => $_ENV['TAGLINE'] ?? 'HTML5 Network Speed Test',
        'admin_email' => $_ENV['SPEEDTEST_EMAIL'] ?? ''
    ],
    'telemetry' => [
        'password' => $_ENV['SPEEDTEST_PASSWORD'] ?? '',
        'enable_id_obfuscation' => filter_var($_ENV['TELEMETRY_OBFUSCATION'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'redact_ip_addresses' => filter_var($_ENV['TELEMETRY_REDACT_IP'] ?? false, FILTER_VALIDATE_BOOLEAN)
    ],
    'ipinfo' => [
        'apikey' => $_ENV['IPINFO_APIKEY'] ?? '',
        'offline_db' => __DIR__ . '/' . basename($_ENV['IPINFO_OFFLINE_DB'] ?? 'country_asn.mmdb')
    ]
];
