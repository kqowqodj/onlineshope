<?php

// Sends server-side Pendo Track Events to the Pendo Track API.
// The Pendo Track Event shared secret is read from the PENDO_TRACK_EVENT_SECRET environment
// variable (for example `SetEnv` in the Apache config). Never commit the secret to this repository.
// Tracking must never break the shop, so every failure is only written to the error log.
function pendo_track($event, $properties = []) {
    try {
        $secret = trim((string) getenv('PENDO_TRACK_EVENT_SECRET'));
        if ($secret === '') {
            error_log("pendo_track: '$event' was not sent (PENDO_TRACK_EVENT_SECRET is not set)");
            return;
        }
        if (!function_exists('curl_init')) {
            error_log("pendo_track: '$event' was not sent (the PHP curl extension is not enabled)");
            return;
        }

        // Pendo limits the properties JSON to 512 bytes, so long text values are shortened.
        foreach ($properties as $key => $value) {
            if (is_string($value)) {
                $properties[$key] = function_exists('mb_substr')
                    ? mb_substr($value, 0, 100, 'UTF-8')
                    : substr($value, 0, 100);
            }
        }

        $payload = json_encode([
            'type' => 'track',
            'event' => $event,
            // The shop has no login or sessions yet, so there is no visitor or account id to send.
            'visitorId' => 'system',
            'accountId' => 'system',
            'timestamp' => (int) (microtime(true) * 1000),
            'properties' => $properties,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($payload === false) {
            error_log("pendo_track: '$event' was not sent (" . json_last_error_msg() . ')');
            return;
        }

        $ch = curl_init('https://data.pendo.io/data/track');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-pendo-integration-key: ' . $secret],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 3,
        ]);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($response === false || $status >= 300) {
            error_log("pendo_track: '$event' was not sent (" . ($response === false ? curl_error($ch) : "HTTP $status") . ')');
        }
    } catch (Throwable $e) {
        error_log("pendo_track: '$event' was not sent (" . $e->getMessage() . ')');
    }
}
