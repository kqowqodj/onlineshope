<?php

// Pendo Track Events sent from the server.
//
// The add product, edit product and add to cart forms post to PHP scripts that
// redirect right away, so whether the action worked is only known here, on the
// server. pendo_track() reports it to Pendo's Track API.
//
// Set the PENDO_TRACK_EVENT_SECRET environment variable to the Pendo track
// event secret (for example with SetEnv in the Apache config). The secret is
// not stored in the code, and no events are sent while it is not set.
// Tracking never stops a page: problems are only written to the PHP error log.

function pendo_track(string $event, array $properties = []): void
{
    $secret = getenv('PENDO_TRACK_EVENT_SECRET');
    if (!$secret) {
        error_log("Pendo track event $event not sent: PENDO_TRACK_EVENT_SECRET is not set");
        return;
    }
    if (!function_exists('curl_init')) {
        error_log("Pendo track event $event not sent: the PHP curl extension is not enabled");
        return;
    }

    try {
        // Leave out unknown values and keep text short: Pendo accepts at most
        // 512 bytes of properties per event.
        foreach ($properties as $key => $value) {
            if ($value === null) {
                unset($properties[$key]);
            } elseif (is_string($value)) {
                $properties[$key] = function_exists('mb_substr')
                    ? mb_substr($value, 0, 100, 'UTF-8')
                    : substr($value, 0, 100);
            }
        }

        $body = json_encode([
            'type' => 'track',
            'event' => $event,
            // The shop has no login, so there is no user or account id to send.
            'visitorId' => 'system',
            'accountId' => 'system',
            'timestamp' => (int) round(microtime(true) * 1000),
            'properties' => (object) $properties,
            'context' => ['userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? ''],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($body === false) {
            error_log("Pendo track event $event not sent: " . json_last_error_msg());
            return;
        }

        $request = curl_init('https://data.pendo.io/data/track');
        curl_setopt_array($request, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-pendo-integration-key: ' . $secret,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 3,
        ]);
        $response = curl_exec($request);
        $status = curl_getinfo($request, CURLINFO_HTTP_CODE);
        if ($response === false || $status >= 300) {
            error_log("Pendo track event $event failed: " . ($response === false ? curl_error($request) : "HTTP $status"));
        }
    } catch (Throwable $e) {
        error_log("Pendo track event $event failed: " . $e->getMessage());
    }
}

// Returns numeric input (such as a price typed into a form) as a number, or
// null so that pendo_track() leaves the property out.
function pendo_track_number($value)
{
    return is_numeric($value) ? $value + 0 : null;
}

?>
