<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Telemetry
{
    /**
     * Insert a new speedtest result.
     *
     * @param string $ip
     * @param string|null $ispinfo
     * @param string|null $extra
     * @param string $ua
     * @param string $lang
     * @param string|null $dl
     * @param string|null $ul
     * @param string|null $ping
     * @param string|null $jitter
     * @param string|null $log
     * @return int|false
     */
    public static function insert(
        string $ip,
        ?string $ispinfo,
        ?string $extra,
        string $ua,
        string $lang,
        ?string $dl,
        ?string $ul,
        ?string $ping,
        ?string $jitter,
        ?string $log
    ) {
        $db = Database::getConnection();
        if (!$db) {
            return false;
        }

        $stmt = $db->prepare('
            INSERT INTO speedtest_users (ip, ispinfo, extra, ua, lang, dl, ul, ping, jitter, log)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        
        $success = $stmt->execute([$ip, $ispinfo, $extra, $ua, $lang, $dl, $ul, $ping, $jitter, $log]);
        
        return $success ? (int)$db->lastInsertId() : false;
    }

    /**
     * Retrieve a speedtest result by its database ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        if (!$db) {
            return null;
        }

        $stmt = $db->prepare('SELECT * FROM speedtest_users WHERE id = ?');
        $stmt->execute([$id]);
        
        $row = $stmt->fetch();
        return $row ? $row : null;
    }

    /**
     * Get the latest 100 speedtest entries.
     *
     * @return array
     */
    public static function getLatest(): array
    {
        $db = Database::getConnection();
        if (!$db) {
            return [];
        }

        $stmt = $db->query('SELECT * FROM speedtest_users ORDER BY timestamp DESC LIMIT 100');
        return $stmt->fetchAll() ?: [];
    }
}
