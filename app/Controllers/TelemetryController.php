<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Traits\RequestHelper;
use App\Services\TelemetryService;
use App\Models\Telemetry;
use Exception;

class TelemetryController extends Controller
{
    use RequestHelper;

    protected $telemetryService;

    public function __construct()
    {
        $this->telemetryService = new TelemetryService();
    }

    /**
     * Store new speedtest telemetry metrics.
     */
    public function store(): void
    {
        $data = [
            'ip' => $this->getClientIp(),
            'ispinfo' => $_POST['ispinfo'] ?? null,
            'extra' => $_POST['extra'] ?? null,
            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'lang' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en',
            'dl' => $_POST['dl'] ?? null,
            'ul' => $_POST['ul'] ?? null,
            'ping' => $_POST['ping'] ?? null,
            'jitter' => $_POST['jitter'] ?? null,
            'log' => $_POST['log'] ?? null,
        ];

        try {
            $id = $this->telemetryService->saveResult($data);
            $this->setNoCacheHeaders();
            echo 'id ' . $id;
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
        }
        exit();
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
