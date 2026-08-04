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
        $this->render('home', [
            'title' => 'LibreSpeed MVC',
            'version' => '1.0.0-mvc'
        ]);
    }
}
