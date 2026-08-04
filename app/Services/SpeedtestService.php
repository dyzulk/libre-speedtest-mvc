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
        // Placeholder for ISP detection. In future, query GeoIP/IPInfo.
        return [
            'processedString' => $ip . ' - Private/Local Network',
            'rawIspInfo' => ''
        ];
    }
}
