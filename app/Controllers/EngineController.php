<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Traits\RequestHelper;
use App\Services\SpeedtestService;

class EngineController extends Controller
{
    use RequestHelper;

    protected $speedtestService;

    public function __construct()
    {
        $this->speedtestService = new SpeedtestService();
    }

    /**
     * Handles latency and upload bandwidth tests.
     */
    public function empty(): void
    {
        header('HTTP/1.1 200 OK');
        $this->setCorsHeaders();
        $this->setNoCacheHeaders();
        header('Connection: keep-alive');
        exit();
    }

    /**
     * Streams random binary data for download tests.
     */
    public function garbage(): void
    {
        @ini_set('zlib.output_compression', 'Off');
        @ini_set('output_buffering', 'Off');
        @ini_set('output_handler', '');

        $ckSize = 4;
        if (isset($_GET['ckSize']) && ctype_digit($_GET['ckSize'])) {
            $ckSize = (int)$_GET['ckSize'];
            if ($ckSize <= 0) {
                $ckSize = 4;
            } elseif ($ckSize > 1024) {
                $ckSize = 1024;
            }
        }

        header('HTTP/1.1 200 OK');
        $this->setCorsHeaders();

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=random.dat');
        header('Content-Transfer-Encoding: binary');
        $this->setNoCacheHeaders();

        $this->speedtestService->generateGarbage($ckSize);
        exit();
    }

    /**
     * Detects client IP and returns ISP details.
     */
    public function getIP(): void
    {
        $ip = $this->getClientIp();
        $this->setCorsHeaders();
        
        $details = $this->speedtestService->getIspDetails($ip);
        $this->json($details);
    }
}
