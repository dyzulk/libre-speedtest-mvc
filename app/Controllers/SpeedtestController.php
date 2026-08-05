<?php

namespace App\Controllers;

use App\Core\Controller;

class SpeedtestController extends Controller
{
    /**
     * Display the main speedtest page.
     *
     * @return void
     */
    public function index(): void
    {
        $config = require __DIR__ . '/../Config/config.php';
        $useNew = $config['app']['use_new_design'] ?? true;

        // Cookie override set by client-side localStorage sync
        $designCookie = $_COOKIE['design_preference'] ?? null;
        if ($designCookie === 'modern') {
            $useNew = true;
        } elseif ($designCookie === 'classic') {
            $useNew = false;
        }

        $view = $useNew ? 'home_modern' : 'home_classic';
        $currentDesign = $useNew ? 'modern' : 'classic';

        $this->render($view, [
            'title' => $config['app']['title'] ?? 'LibreSpeed MVC',
            'tagline' => $config['app']['tagline'] ?? 'HTML5 Network Speed Test',
            'admin_email' => $config['app']['admin_email'] ?? '',
            'version' => '1.0.0-mvc',
            'current_design' => $currentDesign,
            'no_layout' => true,
        ]);
    }
}
