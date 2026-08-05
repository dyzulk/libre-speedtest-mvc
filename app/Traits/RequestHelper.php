<?php

namespace App\Traits;

trait RequestHelper
{
    /**
     * Normalize and validate an IP address candidate from a request header.
     *
     * @param string $raw
     * @param int    $extraFlags
     * @return string|false
     */
    protected function normalizeCandidateIp(string $raw, int $extraFlags = 0)
    {
        $ip = trim($raw);
        if (($pos = strpos($ip, ',')) !== false) {
            $ip = trim(substr($ip, 0, $pos));
        }
        if ($ip === '') {
            return false;
        }
        return filter_var($ip, FILTER_VALIDATE_IP, $extraFlags);
    }

    /**
     * Get the client's IP address.
     *
     * @return string
     */
    protected function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IPV6'])) {
            $ip = $this->normalizeCandidateIp($_SERVER['HTTP_CF_CONNECTING_IPV6'], FILTER_FLAG_IPV6);
            if ($ip !== false) {
                return preg_replace('/^::ffff:/', '', $ip);
            }
        }

        foreach (['HTTP_CLIENT_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR'] as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $this->normalizeCandidateIp($_SERVER[$header]);
                if ($ip !== false) {
                    return preg_replace('/^::ffff:/', '', $ip);
                }
            }
        }

        $ip = $this->normalizeCandidateIp($_SERVER['REMOTE_ADDR'] ?? '');
        if ($ip !== false) {
            return preg_replace('/^::ffff:/', '', $ip);
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Set CORS headers if requested by client.
     *
     * @return void
     */
    protected function setCorsHeaders(): void
    {
        if (isset($_GET['cors'])) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST');
            header('Access-Control-Allow-Headers: Content-Encoding, Content-Type');
        }
    }

    /**
     * Set Cache-Control headers to disable browser caching.
     *
     * @return void
     */
    protected function setNoCacheHeaders(): void
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, s-maxage=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
    }
}
