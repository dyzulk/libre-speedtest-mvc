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
}
