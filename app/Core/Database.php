<?php

namespace App\Core;

use PDO;
use Exception;

class Database
{
    private static $connection = null;

    /**
     * Get the PDO database connection.
     *
     * @return PDO|null
     */
    public static function getConnection(): ?PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $config = require __DIR__ . '/../Config/config.php';
        $dbConfig = $config['db'];
        $type = $dbConfig['type'] ?? 'sqlite';

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];

            if ($type === 'sqlite') {
                $file = $dbConfig['sqlite']['file'];
                $dir = dirname($file);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                
                self::$connection = new PDO('sqlite:' . $file, null, null, $options);
                
                // Set up the sqlite table automatically if it doesn't exist
                self::$connection->exec('
                    CREATE TABLE IF NOT EXISTS `speedtest_users` (
                        `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                        `ispinfo` TEXT,
                        `extra` TEXT,
                        `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `ip` TEXT NOT NULL,
                        `ua` TEXT NOT NULL,
                        `lang` TEXT NOT NULL,
                        `dl` TEXT,
                        `ul` TEXT,
                        `ping` TEXT,
                        `jitter` TEXT,
                        `log` TEXT
                    );
                ');
            } elseif ($type === 'mysql') {
                $mysql = $dbConfig['mysql'];
                $dsn = "mysql:host={$mysql['host']};port={$mysql['port']};dbname={$mysql['dbname']};charset=utf8mb4";
                self::$connection = new PDO($dsn, $mysql['username'], $mysql['password'], $options);
            }
        } catch (Exception $e) {
            error_log('Database Connection Error: ' . $e->getMessage());
            self::$connection = null;
        }

        return self::$connection;
    }
}
