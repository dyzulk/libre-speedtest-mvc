<?php

namespace App\Core;

use PDO;
use Exception;

class Database
{
    private static $connection = null;
    private static $driver = null;

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

            switch ($type) {
                case 'sqlite':
                    self::$connection = self::connectSqlite($dbConfig['sqlite'], $options);
                    break;
                case 'mysql':
                    self::$connection = self::connectMysql($dbConfig['mysql'], $options);
                    break;
                case 'postgresql':
                    self::$connection = self::connectPostgresql($dbConfig['postgresql'], $options);
                    break;
                case 'mssql':
                    self::$connection = self::connectMssql($dbConfig['mssql'], $options);
                    break;
                default:
                    throw new Exception("Unsupported database type: {$type}");
            }

            // Store the PDO driver name and auto-create the table
            if (self::$connection) {
                self::$driver = self::$connection->getAttribute(PDO::ATTR_DRIVER_NAME);
                self::$connection->exec(Schema::getCreateTableSQL(self::$driver));
            }
        } catch (Exception $e) {
            error_log('Database Connection Error: ' . $e->getMessage());
            self::$connection = null;
        }

        return self::$connection;
    }

    /**
     * Get the active PDO driver name.
     *
     * @return string|null Driver name (sqlite, mysql, pgsql, sqlsrv) or null if not connected.
     */
    public static function getDriver(): ?string
    {
        if (self::$driver === null && self::$connection !== null) {
            self::$driver = self::$connection->getAttribute(PDO::ATTR_DRIVER_NAME);
        }
        return self::$driver;
    }

    /**
     * Connect to SQLite database.
     *
     * @param array $config
     * @param array $options
     * @return PDO
     */
    private static function connectSqlite(array $config, array $options): PDO
    {
        $file = $config['file'];
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return new PDO('sqlite:' . $file, null, null, $options);
    }

    /**
     * Connect to MySQL database.
     *
     * @param array $config
     * @param array $options
     * @return PDO
     */
    private static function connectMysql(array $config, array $options): PDO
    {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";
        return new PDO($dsn, $config['username'], $config['password'], $options);
    }

    /**
     * Connect to PostgreSQL database.
     *
     * @param array $config
     * @param array $options
     * @return PDO
     */
    private static function connectPostgresql(array $config, array $options): PDO
    {
        $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        return new PDO($dsn, $config['username'], $config['password'], $options);
    }

    /**
     * Connect to MSSQL database.
     *
     * @param array $config
     * @param array $options
     * @return PDO
     */
    private static function connectMssql(array $config, array $options): PDO
    {
        $dsn = "sqlsrv:Server={$config['host']}";
        if (!empty($config['port'])) {
            $dsn .= ",{$config['port']}";
        }
        $dsn .= ";Database={$config['dbname']}";

        if (isset($config['trust_cert'])) {
            $dsn .= ";TrustServerCertificate=" . ($config['trust_cert'] ? '1' : '0');
        }

        if ($config['win_auth']) {
            return new PDO($dsn, null, null, $options);
        }

        return new PDO($dsn, $config['username'], $config['password'], $options);
    }
}
