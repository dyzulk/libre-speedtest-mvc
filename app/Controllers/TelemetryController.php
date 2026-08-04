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
     * @param string|null $id
     */
    public function show(?string $id = null): void
    {
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }

        if (!$id) {
            http_response_code(404);
            echo 'Result not found';
            exit();
        }

        $row = Telemetry::find((int)$id);
        if (!$row) {
            http_response_code(404);
            echo 'Result not found';
            exit();
        }

        // Check if browser/client is requesting the raw image
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (strpos($accept, 'image/') !== false && strpos($accept, 'text/html') === false) {
            $this->drawImage($row);
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
     * Draw telemetry result sharing card image using GD.
     *
     * @param array $result
     * @return void
     */
    private function drawImage(array $result): void
    {
        // format values for the image
        $dl = $this->formatMetric($result['dl'] ?? 0);
        $ul = $this->formatMetric($result['ul'] ?? 0);
        $ping = $this->formatMetric($result['ping'] ?? 0);
        $jit = $this->formatMetric($result['jitter'] ?? 0);
        $timestamp = $result['timestamp'];

        $ispinfo = '';
        if (!empty($result['ispinfo'])) {
            $ispinfoData = json_decode($result['ispinfo'], true);
            if (isset($ispinfoData['processedString'])) {
                $ispinfo = $ispinfoData['processedString'];
            } else {
                $ispinfo = $result['ispinfo'];
            }
            $dash = strpos($ispinfo, '-');
            if ($dash !== false) {
                $ispinfo = substr($ispinfo, $dash + 2);
                $par = strrpos($ispinfo, '(');
                if ($par !== false) {
                    $ispinfo = substr($ispinfo, 0, $par);
                }
            }
        }
        $ispinfo = trim($ispinfo);

        // initialize the image
        $SCALE = 1.25;
        $SMALL_SEP = 8 * $SCALE;
        $WIDTH = (int)(400 * $SCALE);
        $HEIGHT = (int)(229 * $SCALE);
        $im = imagecreatetruecolor($WIDTH, $HEIGHT);
        $BACKGROUND_COLOR = imagecolorallocate($im, 255, 255, 255);

        // configure fonts
        $fontDir = __DIR__ . '/../../public/assets/fonts/';
        $FONT_LABEL = $fontDir . 'OpenSans-Semibold.ttf';
        $FONT_METER = $fontDir . 'OpenSans-Light.ttf';
        $FONT_MEASURE = $fontDir . 'OpenSans-Semibold.ttf';
        $FONT_ISP = $fontDir . 'OpenSans-Semibold.ttf';
        $FONT_TIMESTAMP = $fontDir . 'OpenSans-Light.ttf';
        $FONT_WATERMARK = $fontDir . 'OpenSans-Light.ttf';

        // configure text colors
        $TEXT_COLOR_LABEL = imagecolorallocate($im, 40, 40, 40);
        $TEXT_COLOR_PING_METER = imagecolorallocate($im, 170, 96, 96);
        $TEXT_COLOR_DL_METER = imagecolorallocate($im, 96, 96, 170);
        $TEXT_COLOR_UL_METER = imagecolorallocate($im, 96, 96, 96);
        $TEXT_COLOR_MEASURE = imagecolorallocate($im, 40, 40, 40);
        $TEXT_COLOR_ISP = imagecolorallocate($im, 40, 40, 40);
        $SEPARATOR_COLOR = imagecolorallocate($im, 192, 192, 192);
        $TEXT_COLOR_TIMESTAMP = imagecolorallocate($im, 160, 160, 160);
        $TEXT_COLOR_WATERMARK = imagecolorallocate($im, 160, 160, 160);

        // configure positioning or the different parts on the image
        $POSITION_X_PING = 125 * $SCALE;
        $POSITION_Y_PING_LABEL = 24 * $SCALE;
        $POSITION_Y_PING_METER = 60 * $SCALE;
        $POSITION_Y_PING_MEASURE = 60 * $SCALE;

        $POSITION_X_JIT = 275 * $SCALE;
        $POSITION_Y_JIT_LABEL = 24 * $SCALE;
        $POSITION_Y_JIT_METER = 60 * $SCALE;
        $POSITION_Y_JIT_MEASURE = 60 * $SCALE;

        $POSITION_X_DL = 120 * $SCALE;
        $POSITION_Y_DL_LABEL = 105 * $SCALE;
        $POSITION_Y_DL_METER = 143 * $SCALE;
        $POSITION_Y_DL_MEASURE = 169 * $SCALE;

        $POSITION_X_UL = 280 * $SCALE;
        $POSITION_Y_UL_LABEL = 105 * $SCALE;
        $POSITION_Y_UL_METER = 143 * $SCALE;
        $POSITION_Y_UL_MEASURE = 169 * $SCALE;

        $POSITION_X_ISP = 4 * $SCALE;
        $POSITION_Y_ISP = 205 * $SCALE;

        $SEPARATOR_Y = 211 * $SCALE;

        $POSITION_X_TIMESTAMP = 4 * $SCALE;
        $POSITION_Y_TIMESTAMP = 223 * $SCALE;

        $POSITION_Y_WATERMARK = 223 * $SCALE;

        // configure labels
        $MBPS_TEXT = 'Mbit/s';
        $MS_TEXT = 'ms';
        $PING_TEXT = 'Ping';
        $JIT_TEXT = 'Jitter';
        $DL_TEXT = 'Download';
        $UL_TEXT = 'Upload';
        $WATERMARK_TEXT = 'LibreSpeed';

        // create text boxes for each part of the image
        $mbpsBbox = imageftbbox(12 * $SCALE, 0, $FONT_MEASURE, $MBPS_TEXT);
        $msBbox = imageftbbox(12 * $SCALE, 0, $FONT_MEASURE, $MS_TEXT);
        $pingBbox = imageftbbox(14 * $SCALE, 0, $FONT_LABEL, $PING_TEXT);
        $pingMeterBbox = imageftbbox(20 * $SCALE, 0, $FONT_METER, $ping);
        $jitBbox = imageftbbox(14 * $SCALE, 0, $FONT_LABEL, $JIT_TEXT);
        $jitMeterBbox = imageftbbox(20 * $SCALE, 0, $FONT_METER, $jit);
        $dlBbox = imageftbbox(16 * $SCALE, 0, $FONT_LABEL, $DL_TEXT);
        $dlMeterBbox = imageftbbox(22 * $SCALE, 0, $FONT_METER, $dl);
        $ulBbox = imageftbbox(16 * $SCALE, 0, $FONT_LABEL, $UL_TEXT);
        $ulMeterBbox = imageftbbox(22 * $SCALE, 0, $FONT_METER, $ul);
        $watermarkBbox = imageftbbox(8 * $SCALE, 0, $FONT_WATERMARK, $WATERMARK_TEXT);
        $POSITION_X_WATERMARK = $WIDTH - $watermarkBbox[4] - 4 * $SCALE;

        // put the parts together to draw the image
        imagefilledrectangle($im, 0, 0, $WIDTH, $HEIGHT, $BACKGROUND_COLOR);
        // ping
        imagefttext($im, 14 * $SCALE, 0, (int)($POSITION_X_PING - $pingBbox[4] / 2), (int)$POSITION_Y_PING_LABEL, $TEXT_COLOR_LABEL, $FONT_LABEL, $PING_TEXT);
        imagefttext($im, 20 * $SCALE, 0, (int)($POSITION_X_PING - $pingMeterBbox[4] / 2 - $msBbox[4] / 2 - $SMALL_SEP / 2), (int)$POSITION_Y_PING_METER, $TEXT_COLOR_PING_METER, $FONT_METER, $ping);
        imagefttext($im, 12 * $SCALE, 0, (int)($POSITION_X_PING + $pingMeterBbox[4] / 2 + $SMALL_SEP / 2 - $msBbox[4] / 2), (int)$POSITION_Y_PING_MEASURE, $TEXT_COLOR_MEASURE, $FONT_MEASURE, $MS_TEXT);
        // jitter
        imagefttext($im, 14 * $SCALE, 0, (int)($POSITION_X_JIT - $jitBbox[4] / 2), (int)$POSITION_Y_JIT_LABEL, $TEXT_COLOR_LABEL, $FONT_LABEL, $JIT_TEXT);
        imagefttext($im, 20 * $SCALE, 0, (int)($POSITION_X_JIT - $jitMeterBbox[4] / 2 - $msBbox[4] / 2 - $SMALL_SEP / 2), (int)$POSITION_Y_JIT_METER, $TEXT_COLOR_PING_METER, $FONT_METER, $jit);
        imagefttext($im, 12 * $SCALE, 0, (int)($POSITION_X_JIT + $jitMeterBbox[4] / 2 + $SMALL_SEP / 2 - $msBbox[4] / 2), (int)$POSITION_Y_JIT_MEASURE, $TEXT_COLOR_MEASURE, $FONT_MEASURE, $MS_TEXT);
        // dl
        imagefttext($im, 16 * $SCALE, 0, (int)($POSITION_X_DL - $dlBbox[4] / 2), (int)$POSITION_Y_DL_LABEL, $TEXT_COLOR_LABEL, $FONT_LABEL, $DL_TEXT);
        imagefttext($im, 22 * $SCALE, 0, (int)($POSITION_X_DL - $dlMeterBbox[4] / 2), (int)$POSITION_Y_DL_METER, $TEXT_COLOR_DL_METER, $FONT_METER, $dl);
        imagefttext($im, 12 * $SCALE, 0, (int)($POSITION_X_DL - $mbpsBbox[4] / 2), (int)$POSITION_Y_DL_MEASURE, $TEXT_COLOR_MEASURE, $FONT_MEASURE, $MBPS_TEXT);
        // ul
        imagefttext($im, 16 * $SCALE, 0, (int)($POSITION_X_UL - $ulBbox[4] / 2), (int)$POSITION_Y_UL_LABEL, $TEXT_COLOR_LABEL, $FONT_LABEL, $UL_TEXT);
        imagefttext($im, 22 * $SCALE, 0, (int)($POSITION_X_UL - $ulMeterBbox[4] / 2), (int)$POSITION_Y_UL_METER, $TEXT_COLOR_UL_METER, $FONT_METER, $ul);
        imagefttext($im, 12 * $SCALE, 0, (int)($POSITION_X_UL - $mbpsBbox[4] / 2), (int)$POSITION_Y_UL_MEASURE, $TEXT_COLOR_MEASURE, $FONT_MEASURE, $MBPS_TEXT);
        // isp
        imagefttext($im, 9 * $SCALE, 0, (int)$POSITION_X_ISP, (int)$POSITION_Y_ISP, $TEXT_COLOR_ISP, $FONT_ISP, $ispinfo);
        // separator
        imagefilledrectangle($im, 0, (int)$SEPARATOR_Y, $WIDTH, (int)$SEPARATOR_Y, $SEPARATOR_COLOR);
        // timestamp
        imagefttext($im, 8 * $SCALE, 0, (int)$POSITION_X_TIMESTAMP, (int)$POSITION_Y_TIMESTAMP, $TEXT_COLOR_TIMESTAMP, $FONT_TIMESTAMP, $timestamp);
        // watermark
        imagefttext($im, 8 * $SCALE, 0, (int)$POSITION_X_WATERMARK, (int)$POSITION_Y_WATERMARK, $TEXT_COLOR_WATERMARK, $FONT_WATERMARK, $WATERMARK_TEXT);

        // send the image to the browser
        header('Content-Type: image/png');
        imagepng($im);
        imagedestroy($im);
    }

    private function formatMetric($d): string
    {
        $d = (float)$d;
        if ($d < 10) {
            return number_format($d, 2, '.', '');
        }
        if ($d < 100) {
            return number_format($d, 1, '.', '');
        }
        return number_format($d, 0, '.', '');
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
