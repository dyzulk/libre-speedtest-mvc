<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Telemetry;

class TelemetryController extends Controller
{
    /**
     * Store new speedtest telemetry metrics.
     */
    public function store(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ispinfo = $_POST['ispinfo'] ?? null;
        $extra = $_POST['extra'] ?? null;
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en';
        
        $dl = $_POST['dl'] ?? null;
        $ul = $_POST['ul'] ?? null;
        $ping = $_POST['ping'] ?? null;
        $jitter = $_POST['jitter'] ?? null;
        $log = $_POST['log'] ?? null;

        $id = Telemetry::insert($ip, $ispinfo, $extra, $ua, $lang, $dl, $ul, $ping, $jitter, $log);
        if ($id === false) {
            http_response_code(500);
            echo 'Failed to store results';
            exit();
        }

        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, s-maxage=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');

        echo 'id ' . $id;
    }

    /**
     * Show details of a specific speedtest.
     *
     * @param string $id
     */
    public function show(string $id): void
    {
        $row = Telemetry::find((int)$id);
        if (!$row) {
            http_response_code(404);
            echo 'Result not found';
            exit();
        }

        // Output JSON if requested, otherwise render HTML template
        if (isset($_GET['json']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            $this->json($row);
        } else {
            $this->render('result', [
                'title' => 'Speedtest Result',
                'result' => $row
            ]);
        }
    }

    /**
     * Render the admin speedtest statistics logs.
     */
    public function stats(): void
    {
        $results = Telemetry::getLatest();
        $this->render('stats', [
            'title' => 'Telemetry Logs',
            'results' => $results
        ]);
    }
}
