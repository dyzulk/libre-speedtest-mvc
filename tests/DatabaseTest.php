<?php

/**
 * DatabaseTest.php
 *
 * Standalone test script to verify database schema translation and query logic.
 * Runs WITHOUT any external database server -- uses SQL string assertions
 * and in-memory SQLite for functional verification.
 *
 * Usage: php tests/DatabaseTest.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Schema;

$passed = 0;
$failed = 0;

function assert_equals($expected, $actual, string $testName): void
{
    global $passed, $failed;
    if ($expected === $actual) {
        echo "  [PASS] {$testName}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$testName}\n";
        echo "    Expected: " . var_export($expected, true) . "\n";
        echo "    Actual:   " . var_export($actual, true) . "\n";
        $failed++;
    }
}

function assert_contains(string $haystack, string $needle, string $testName): void
{
    global $passed, $failed;
    if (strpos($haystack, $needle) !== false) {
        echo "  [PASS] {$testName}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$testName}\n";
        echo "    Expected to find: \"{$needle}\"\n";
        echo "    In: \"{$haystack}\"\n";
        $failed++;
    }
}

// ======================================================================
// 1. SQL Syntax Assertion Tests (no database needed)
// ======================================================================

echo "\n=== Schema::castAsFloat() Tests ===\n";

assert_equals(
    'CAST(dl AS REAL)',
    Schema::castAsFloat('sqlite', 'dl'),
    'SQLite CAST uses REAL'
);

assert_equals(
    'CAST(dl AS DECIMAL(10,2))',
    Schema::castAsFloat('mysql', 'dl'),
    'MySQL CAST uses DECIMAL(10,2)'
);

assert_equals(
    'CAST(dl AS NUMERIC)',
    Schema::castAsFloat('pgsql', 'dl'),
    'PostgreSQL CAST uses NUMERIC'
);

assert_equals(
    'CAST(dl AS FLOAT)',
    Schema::castAsFloat('sqlsrv', 'dl'),
    'MSSQL CAST uses FLOAT'
);

echo "\n=== Schema::getCreateTableSQL() DDL Tests ===\n";

// SQLite
$sqliteDDL = Schema::getCreateTableSQL('sqlite');
assert_contains($sqliteDDL, 'AUTOINCREMENT', 'SQLite DDL uses AUTOINCREMENT');
assert_contains($sqliteDDL, 'CREATE TABLE IF NOT EXISTS', 'SQLite DDL uses IF NOT EXISTS');

// MySQL
$mysqlDDL = Schema::getCreateTableSQL('mysql');
assert_contains($mysqlDDL, 'AUTO_INCREMENT', 'MySQL DDL uses AUTO_INCREMENT');
assert_contains($mysqlDDL, 'ENGINE=InnoDB', 'MySQL DDL uses InnoDB engine');
assert_contains($mysqlDDL, 'utf8mb4', 'MySQL DDL uses utf8mb4 charset');

// PostgreSQL
$pgsqlDDL = Schema::getCreateTableSQL('pgsql');
assert_contains($pgsqlDDL, 'SERIAL PRIMARY KEY', 'PostgreSQL DDL uses SERIAL');
assert_contains($pgsqlDDL, 'DEFAULT NOW()', 'PostgreSQL DDL uses NOW()');

// MSSQL
$mssqlDDL = Schema::getCreateTableSQL('sqlsrv');
assert_contains($mssqlDDL, 'IDENTITY(1,1)', 'MSSQL DDL uses IDENTITY');
assert_contains($mssqlDDL, 'GETDATE()', 'MSSQL DDL uses GETDATE()');
assert_contains($mssqlDDL, 'NVARCHAR', 'MSSQL DDL uses NVARCHAR');
assert_contains($mssqlDDL, 'sys.tables', 'MSSQL DDL uses sys.tables check');

// ======================================================================
// 2. In-Memory SQLite Functional Tests (no file, no server)
// ======================================================================

echo "\n=== In-Memory SQLite Functional Tests ===\n";

try {
    $pdo = new PDO('sqlite::memory:', null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Create table using Schema translator
    $pdo->exec(Schema::getCreateTableSQL('sqlite'));
    echo "  [PASS] Table created successfully in memory\n";
    $passed++;

    // Insert test data
    $stmt = $pdo->prepare('
        INSERT INTO speedtest_users (ip, ispinfo, extra, ua, lang, dl, ul, ping, jitter, log)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    $stmt->execute(['127.0.0.1', '{"org":"Test ISP"}', null, 'TestAgent/1.0', 'en', '100.50', '50.25', '5.3', '1.2', null]);
    $stmt->execute(['192.168.1.1', '{"org":"Another ISP"}', null, 'TestAgent/2.0', 'id', '200.75', '100.50', '10.1', '2.5', null]);

    $lastId = (int)$pdo->lastInsertId();
    assert_equals(2, $lastId, 'Last insert ID is 2 after two inserts');

    // Test find by ID
    $findStmt = $pdo->prepare('SELECT * FROM speedtest_users WHERE id = ?');
    $findStmt->execute([1]);
    $row = $findStmt->fetch();
    assert_equals('127.0.0.1', $row['ip'], 'Find by ID returns correct IP');
    assert_equals('100.50', $row['dl'], 'Find by ID returns correct download speed');

    // Test getLatest equivalent
    $latestStmt = $pdo->query('SELECT * FROM speedtest_users ORDER BY timestamp DESC LIMIT 100');
    $rows = $latestStmt->fetchAll();
    assert_equals(2, count($rows), 'getLatest returns 2 rows');

    // Test getSummaryStats with Schema::castAsFloat
    $castDl = Schema::castAsFloat('sqlite', 'dl');
    $castUl = Schema::castAsFloat('sqlite', 'ul');
    $castPing = Schema::castAsFloat('sqlite', 'ping');

    $statsStmt = $pdo->query("
        SELECT 
            COUNT(*) as total_tests,
            AVG({$castDl}) as avg_dl,
            AVG({$castUl}) as avg_ul,
            AVG({$castPing}) as avg_ping
        FROM speedtest_users
    ");
    $stats = $statsStmt->fetch();

    assert_equals(2, (int)$stats['total_tests'], 'Summary stats total_tests = 2');

    $avgDl = round((float)$stats['avg_dl'], 2);
    assert_equals(150.63, $avgDl, 'Summary stats avg_dl = 150.63 (avg of 100.50 and 200.75)');

    $avgUl = round((float)$stats['avg_ul'], 2);
    assert_equals(75.38, $avgUl, 'Summary stats avg_ul = 75.38 (avg of 50.25 and 100.50)');

    echo "  [PASS] In-memory SQLite functional tests complete\n";
    $passed++;

} catch (Exception $e) {
    echo "  [FAIL] In-memory SQLite test crashed: " . $e->getMessage() . "\n";
    $failed++;
}

// ======================================================================
// Summary
// ======================================================================

echo "\n=== Results ===\n";
echo "  Passed: {$passed}\n";
echo "  Failed: {$failed}\n";
echo "  Total:  " . ($passed + $failed) . "\n\n";

exit($failed > 0 ? 1 : 0);
