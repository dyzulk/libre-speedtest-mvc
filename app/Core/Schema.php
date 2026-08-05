<?php

namespace App\Core;

/**
 * Schema provides database-dialect-specific SQL fragments.
 *
 * Centralizes all SQL dialect differences (DDL, CAST expressions,
 * timestamp defaults) so that models remain database-agnostic.
 */
class Schema
{
    /**
     * Returns the CREATE TABLE DDL for the speedtest_users table
     * appropriate to the given PDO driver.
     *
     * @param string $driver PDO driver name (sqlite, mysql, pgsql, sqlsrv)
     * @return string SQL statement
     */
    public static function getCreateTableSQL(string $driver): string
    {
        switch ($driver) {
            case 'mysql':
                return '
                    CREATE TABLE IF NOT EXISTS `speedtest_users` (
                        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        `ispinfo` TEXT,
                        `extra` TEXT,
                        `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `ip` VARCHAR(255) NOT NULL,
                        `ua` TEXT NOT NULL,
                        `lang` VARCHAR(50) NOT NULL,
                        `dl` TEXT,
                        `ul` TEXT,
                        `ping` TEXT,
                        `jitter` TEXT,
                        `log` LONGTEXT
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ';

            case 'pgsql':
                return '
                    CREATE TABLE IF NOT EXISTS speedtest_users (
                        id SERIAL PRIMARY KEY,
                        ispinfo TEXT,
                        extra TEXT,
                        timestamp TIMESTAMP NOT NULL DEFAULT NOW(),
                        ip VARCHAR(255) NOT NULL,
                        ua TEXT NOT NULL,
                        lang VARCHAR(50) NOT NULL,
                        dl TEXT,
                        ul TEXT,
                        ping TEXT,
                        jitter TEXT,
                        log TEXT
                    );
                ';

            case 'sqlsrv':
                return '
                    IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = \'speedtest_users\')
                    CREATE TABLE speedtest_users (
                        id INT IDENTITY(1,1) PRIMARY KEY,
                        ispinfo NVARCHAR(MAX),
                        extra NVARCHAR(MAX),
                        timestamp DATETIME2 NOT NULL DEFAULT GETDATE(),
                        ip NVARCHAR(255) NOT NULL,
                        ua NVARCHAR(MAX) NOT NULL,
                        lang NVARCHAR(50) NOT NULL,
                        dl NVARCHAR(MAX),
                        ul NVARCHAR(MAX),
                        ping NVARCHAR(MAX),
                        jitter NVARCHAR(MAX),
                        log NVARCHAR(MAX)
                    );
                ';

            case 'sqlite':
            default:
                return '
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
                ';
        }
    }

    /**
     * Returns a CAST expression to convert a text column to a floating-point
     * number for the given PDO driver.
     *
     * @param string $driver PDO driver name (sqlite, mysql, pgsql, sqlsrv)
     * @param string $column Column name to cast
     * @return string SQL CAST expression
     */
    public static function castAsFloat(string $driver, string $column): string
    {
        switch ($driver) {
            case 'mysql':
                return "CAST({$column} AS DECIMAL(10,2))";
            case 'pgsql':
                return "CAST({$column} AS NUMERIC)";
            case 'sqlsrv':
                return "CAST({$column} AS FLOAT)";
            case 'sqlite':
            default:
                return "CAST({$column} AS REAL)";
        }
    }
}
