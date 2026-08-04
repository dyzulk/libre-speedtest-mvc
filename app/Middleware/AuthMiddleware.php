<?php

namespace App\Middleware;

use App\Core\Middleware;
use App\Traits\ViewRenderer;

class AuthMiddleware implements Middleware
{
    use ViewRenderer;

    /**
     * Handle telemetry stats authentication.
     *
     * @return void
     */
    public function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $config = require __DIR__ . '/../Config/config.php';
        $adminPassword = $config['telemetry']['password'] ?? '';

        // Block access if password is empty or not configured in .env
        if (empty($adminPassword) || $adminPassword === 'PASSWORD') {
            $this->render('stats_error', [
                'title' => 'Telemetry Logs Error',
                'message' => 'Please configure a secure SPEEDTEST_PASSWORD in your .env file to enable telemetry dashboard access.'
            ]);
            exit();
        }

        // Redirect to login if not authenticated
        if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
            header('Location: /login');
            exit();
        }
    }
}
