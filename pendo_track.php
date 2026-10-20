<?php

// Server-side Pendo Track Events.
//
// insert.php, up.php and insert_card.php decide the outcome of a product or cart action on the
// server and then redirect straight away, so a pendo.track() call in the browser would never run.
// pendo_track() reports those outcomes to Pendo's Track Event API instead.
//
// The Pendo track event secret is read from the PENDO_TRACK_EVENT_SECRET environment variable,
// e.g. "SetEnv PENDO_TRACK_EVENT_SECRET ..." in the Apache config or
// "env[PENDO_TRACK_EVENT_SECRET] = ..." in a PHP-FPM pool. Never commit the secret itself.
//
// Tracking never interrupts the shop: when the secret is missing or Pendo cannot be reached, the
// reason is written to the PHP error log and the request carries on as before.

/**
 * Sends one Track Event to Pendo.
 *
 * @param string $event      Event name, exactly as it is registered in Pendo.
 * @param array  $properties Event metadata. Null values are left out and text is cut to 100
 *                           characters so the payload stays under Pendo's 512-byte limit.
 * @param string $visitor_id The shop has no login yet, so events default to the "system" visitor.
 * @param string $account_id The shop has no accounts yet, so events default to "system".
 * @return bool True when Pendo accepted the event.
 */
function pendo_track($event, $properties = [], $visitor_id = 'system', $account_id = 'system')
{
    try {
        $secret = getenv('PENDO_TRACK_EVENT_SECRET');
        if (!$secret) {
            error_log("Pendo track event '$event' not sent: PENDO_TRACK_EVENT_SECRET is not set");
            return false;
        }

        $clean_properties = [];
        foreach ($properties as $name => $value) {
            if ($value === null) {
                continue;
            }
            // First 100 characters; the u flag keeps multi-byte (e.g. Arabic) text intact.
            if (is_string($value) && preg_match('/^.{0,100}/su', $value, $match)) {
                $value = $match[0];
            }
            $clean_properties[$name] = $value;
        }

        $body = json_encode([
            'type' => 'track',
            'event' => $event,
            'visitorId' => $visitor_id,
            'accountId' => $account_id,
            'timestamp' => (int) round(microtime(true) * 1000),
            'properties' => (object) $clean_properties,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($body === false) {
            error_log("Pendo track event '$event' not sent: " . json_last_error_msg());
            return false;
        }

        error_clear_last();
        $response = @file_get_contents('https://data.pendo.io/data/track', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => ['Content-Type: application/json', 'x-pendo-integration-key: ' . $secret],
                'content' => $body,
                'timeout' => 3, // seconds; keeps the redirect quick if Pendo is slow or unreachable
            ],
        ]));
        if ($response === false) {
            $last_error = error_get_last();
            error_log("Pendo track event '$event' not sent: " . ($last_error ? $last_error['message'] : 'request failed'));
            return false;
        }

        return true;
    } catch (Throwable $e) {
        error_log("Pendo track event '$event' not sent: " . $e->getMessage());
        return false;
    }
}
