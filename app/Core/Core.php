<?php

namespace App\Core;

class Core
{
    /**
     * Bootstraps the application, loading Composer autoloading and environmental settings.
     *
     * @return void
     */
    public static function bootstrap(): void
    {
        // Load Composer Autoloader
        require_once __DIR__ . '/../../vendor/autoload.php';

        // Parse environmental settings
        if (file_exists(__DIR__ . '/../../.env')) {
            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();
        }
    }
}
