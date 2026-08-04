<?php

namespace App\Services;

use App\Models\Telemetry;
use Exception;

class TelemetryService
{
    /**
     * Sanitize and save the speedtest result into the database.
     *
     * @param array $data
     * @return string
     * @throws Exception
     */
    public function saveResult(array $data): string
    {
        $ip = $data['ip'] ?? '127.0.0.1';
        $ispinfo = $data['ispinfo'] ?? null;
        $extra = $data['extra'] ?? null;
        $ua = $data['ua'] ?? 'Unknown';
        $lang = $data['lang'] ?? 'en';
        
        $dl = $data['dl'] ?? null;
        $ul = $data['ul'] ?? null;
        $ping = $data['ping'] ?? null;
        $jitter = $data['jitter'] ?? null;
        $log = $data['log'] ?? null;

        $config = require __DIR__ . '/../Config/config.php';
        $redact = $config['telemetry']['redact_ip_addresses'] ?? false;

        if ($redact) {
            $ip = '0.0.0.0';
            $ipv4_regex = '/(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/';
            $ipv6_regex = '/(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))/';
            $hostname_regex = '/"hostname":"([^\\\\"]|\\\\")*"/';

            if ($ispinfo) {
                $ispinfo = preg_replace($ipv4_regex, '0.0.0.0', $ispinfo);
                $ispinfo = preg_replace($ipv6_regex, '0.0.0.0', $ispinfo);
                $ispinfo = preg_replace($hostname_regex, '"hostname":"REDACTED"', $ispinfo);
            }
            if ($log) {
                $log = preg_replace($ipv4_regex, '0.0.0.0', $log);
                $log = preg_replace($ipv6_regex, '0.0.0.0', $log);
                $log = preg_replace($hostname_regex, '"hostname":"REDACTED"', $log);
            }
        }

        $id = Telemetry::insert($ip, $ispinfo, $extra, $ua, $lang, $dl, $ul, $ping, $jitter, $log);
        if ($id === false) {
            throw new Exception('Database write error during telemetry storage');
        }

        return (string)$id;
    }

    /**
     * Draw telemetry result sharing card image using GD and send it directly to the browser.
     *
     * @param array $result
     * @return void
     */
    public function renderSharingCard(array $result): void
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

    /**
     * Format numbers into standardized telemetry layouts.
     */
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
}
