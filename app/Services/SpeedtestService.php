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

        $config = require __DIR__ . '/../Config/config.php';
        $apiKey = $config['ipinfo']['apikey'] ?? '';

        if (!empty($apiKey)) {
            $apiResult = $this->getIspInfo_ipinfoApi($ip, $apiKey);
            if (!empty($apiResult)) {
                return $apiResult;
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

        $processedString = $ip;
        if (!empty($isp)) {
            $processedString .= ' - ' . $isp;
        }
        if (!empty($country)) {
            $processedString .= ', ' . $country;
        }

        return [
            'processedString' => $processedString,
            'rawIspInfo' => $data
        ];
    }
}
