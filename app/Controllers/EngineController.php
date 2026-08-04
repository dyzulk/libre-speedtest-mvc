<?php

namespace App\Controllers;

use App\Core\Controller;

class EngineController extends Controller
{
    /**
     * Handles latency and upload bandwidth tests.
     */
    public function empty(): void
    {
        header('HTTP/1.1 200 OK');
        
        if (isset($_GET['cors'])) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST');
            header('Access-Control-Allow-Headers: Content-Encoding, Content-Type');
        }

        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, s-maxage=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
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
        if (isset($_GET['cors'])) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST');
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=random.dat');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, s-maxage=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');

        $data = openssl_random_pseudo_bytes(1048576);
        for ($i = 0; $i < $ckSize; $i++) {
            echo $data;
            flush();
        }
        exit();
    }

    /**
     * Detects client IP and returns ISP details placeholder.
     */
    public function getIP(): void
    {
        $ip = $this->getClientIp();
        
        if (isset($_GET['cors'])) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST');
        }

        $processedString = $ip . ' - Private/Local Network';
        
        $this->json([
            'processedString' => $processedString,
            'rawIspInfo' => ''
        ]);
    }

    /**
     * Retrieve client IP addressing.
     */
    protected function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($parts[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
