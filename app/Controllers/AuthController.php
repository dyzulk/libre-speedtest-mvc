<?php

namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller
{
    /**
     * Show the login form.
     *
     * @return void
     */
    public function showLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // If already logged in, redirect to stats
        if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
            header('Location: /stats');
            exit();
        }

        $this->render('stats_login', ['title' => 'Telemetry Logs Login']);
    }

    /**
     * Handle the login form submission.
     *
     * @return void
     */
    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $config = require __DIR__ . '/../Config/config.php';
        $adminPassword = $config['telemetry']['password'] ?? '';
        $submittedPassword = $_POST['password'] ?? '';

        if (!empty($adminPassword) && $submittedPassword === $adminPassword) {
            $_SESSION['logged'] = true;
            header('Location: /stats');
            exit();
        }

        $this->render('stats_login', [
            'title' => 'Telemetry Logs Login',
            'error' => 'Incorrect password'
        ]);
    }

    /**
     * Handle logout.
     *
     * @return void
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['logged'] = false;
        header('Location: /login');
        exit();
    }
}
