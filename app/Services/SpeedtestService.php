<?php

namespace App\Services;

class SpeedtestService
{
    /**
     * Streams random binary data for download tests.
     *
     * @param int $ckSize
     * @return void
     */
    public function generateGarbage(int $ckSize): void
    {
        $data = openssl_random_pseudo_bytes(1048576);
        for ($i = 0; $i < $ckSize; $i++) {
            echo $data;
            flush();
        }
    }

    /**
     * Get client ISP and geographical details.
     *
     * @param string $ip
     * @return array
     */
    public function getIspDetails(string $ip): array
    {
        $localInfo = $this->getLocalOrPrivateIpInfo($ip);
        if (!empty($localInfo)) {
            return [
                'processedString' => $ip . ' - ' . $localInfo,
                'rawIspInfo' => ''
            ];
        }

        if (isset($_GET['isp'])) {
            $config = require __DIR__ . '/../Config/config.php';
            $apiKey = $config['ipinfo']['apikey'] ?? '';

            if (!empty($apiKey)) {
                $apiResult = $this->getIspInfo_ipinfoApi($ip, $apiKey);
                if (!empty($apiResult)) {
                    return $apiResult;
                }
            }

            $offlineResult = $this->getIspInfo_ipinfoOfflineDb($ip);
            if (!empty($offlineResult)) {
                return $offlineResult;
            }
        }

        // Fallback: simple IP representation
        return [
            'processedString' => $ip,
            'rawIspInfo' => ''
        ];
    }

    /**
     * Get local or private IP description if address is non-routable.
     */
    private function getLocalOrPrivateIpInfo(string $ip): ?string
    {
        if ($ip === '::1') {
            return 'localhost IPv6 access';
        }
        if (stripos($ip, 'fe80:') === 0) {
            return 'link-local IPv6 access';
        }
        if (preg_match('/^(fc|fd)([0-9a-f]{0,4}:){1,7}[0-9a-f]{1,4}$/i', $ip) === 1) {
            return 'ULA IPv6 access';
        }
        if (strpos($ip, '127.') === 0) {
            return 'localhost IPv4 access';
        }
        if (strpos($ip, '10.') === 0) {
            return 'private IPv4 access';
        }
        if (preg_match('/^172\.(1[6-9]|2\d|3[01])\./', $ip) === 1) {
            return 'private IPv4 access';
        }
        if (strpos($ip, '192.168.') === 0) {
            return 'private IPv4 access';
        }
        if (strpos($ip, '169.254.') === 0) {
            return 'link-local IPv4 access';
        }
        return null;
    }

    /**
     * Fetch GeoIP/ISP details from ipinfo.io online API.
     */
    private function getIspInfo_ipinfoApi(string $ip, string $apiKey): ?array
    {
        if (empty($apiKey)) {
            return null;
        }

        $url = 'https://ipinfo.io/' . $ip . '/json?token=' . $apiKey;
        $context = stream_context_create([
            'http' => [
                'timeout' => 5
            ]
        ]);

        $json = @file_get_contents($url, false, $context);
        if (!is_string($json)) {
            return null;
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            return null;
        }

        $isp = null;
        if (!empty($data['org']) && is_string($data['org'])) {
            $isp = preg_replace('/AS\\d+\\s/', '', $data['org']);
        } elseif (!empty($data['asn']['name']) && is_string($data['asn']['name'])) {
            $isp = $data['asn']['name'];
        }

        $country = $data['country'] ?? null;
        $distance = null;

        if (isset($_GET['distance']) && ($_GET['distance'] === 'mi' || $_GET['distance'] === 'km') && !empty($data['loc']) && is_string($data['loc'])) {
            $unit = $_GET['distance'];
            $clientLoc = $data['loc'];
            $serverLoc = null;
            $cacheFile = __DIR__ . '/../Config/serverLocation.php';

            if (file_exists($cacheFile) && is_readable($cacheFile)) {
                require $cacheFile;
            }

            if (!is_string($serverLoc) || empty($serverLoc)) {
                $sjson = @file_get_contents('https://ipinfo.io/json?token=' . $apiKey, false, $context);
                if (is_string($sjson)) {
                    $sdata = json_decode($sjson, true);
                    if (is_array($sdata) && !empty($sdata['loc']) && is_string($sdata['loc'])) {
                        $serverLoc = $sdata['loc'];
                        @file_put_contents($cacheFile, "<?php\n\n\$serverLoc = '" . addslashes($serverLoc) . "';\n");
                    }
                }
            }

            if (is_string($serverLoc) && !empty($serverLoc)) {
                list($clientLatitude, $clientLongitude) = explode(',', $clientLoc);
                list($serverLatitude, $serverLongitude) = explode(',', $serverLoc);
                $rad = M_PI / 180;
                $dist = acos(sin($clientLatitude * $rad) * sin($serverLatitude * $rad) + cos($clientLatitude * $rad) * cos($serverLatitude * $rad) * cos(($clientLongitude - $serverLongitude) * $rad)) / $rad * 60 * 1.853;
                if ($unit === 'mi') {
                    $dist /= 1.609344;
                    $dist = round($dist, -1);
                    if ($dist < 15) {
                        $dist = '<15';
                    }
                    $distance = $dist . ' mi';
                } elseif ($unit === 'km') {
                    $dist = round($dist, -1);
                    if ($dist < 20) {
                        $dist = '<20';
                    }
                    $distance = $dist . ' km';
                }
            }
        }

        $processedString = $ip;
        if (!empty($isp)) {
            $processedString .= ' - ' . $isp;
        }
        if (!empty($country)) {
            $processedString .= ', ' . $country;
        }
        if (!empty($distance)) {
            $processedString .= ' (' . $distance . ')';
        }

        return [
            'processedString' => $processedString,
            'rawIspInfo' => $data
        ];
    }

    /**
     * Get offline GeoIP info.
     */
    private function getIspInfo_ipinfoOfflineDb(string $ip): ?array
    {
        $config = require __DIR__ . '/../Config/config.php';
        $dbFile = $config['ipinfo']['offline_db'] ?? (__DIR__ . '/../Config/country_asn.mmdb');
        $pharFile = __DIR__ . '/../Config/geoip2.phar';

        if (PHP_VERSION_ID < 80100 || !file_exists($dbFile) || !is_readable($dbFile)) {
            return null;
        }

        if (file_exists($pharFile) && is_readable($pharFile)) {
            require_once $pharFile;
        }

        if (!class_exists('MaxMind\Db\Reader')) {
            return null;
        }

        try {
            $reader = new \MaxMind\Db\Reader($dbFile);
            $data = $reader->get($ip);
            if (!is_array($data)) {
                return null;
            }

            $asName = $data['as_name'] ?? '';
            $countryName = $data['country_name'] ?? '';
            $processedString = $ip;

            if ($asName !== '') {
                $processedString .= ' - ' . $asName;
            }
            if ($countryName !== '') {
                $processedString .= ', ' . $countryName;
            }

            return [
                'processedString' => $processedString,
                'rawIspInfo' => $data
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
