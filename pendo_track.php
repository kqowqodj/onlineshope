<?php
// Pendo Track Events.
// insert.php, up.php, insert_card.php and del_card.php end with a redirect and
// render no page, so they queue their events in the session with
// pendo_queue_event(). The page they redirect to (index.php or card.php) calls
// pendo_flush_events(), which sends each queued event once with pendo.track().
// Include this file before any output, because it starts the session.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function pendo_queue_event(string $event, array $properties): void
{
    foreach ($properties as $key => $value) {
        // Cap free text such as product names so the properties stay under Pendo's 512-byte limit
        if (is_string($value) && function_exists('mb_substr')) {
            $properties[$key] = mb_substr($value, 0, 100, 'UTF-8');
        }
    }
    $_SESSION['pendo_events'][] = ['event' => $event, 'properties' => $properties];
}

function pendo_flush_events(): void
{
    if (empty($_SESSION['pendo_events'])) {
        return;
    }
    $events = $_SESSION['pendo_events'];
    // Empty the queue so a reload or a later page never sends the same event again
    unset($_SESSION['pendo_events']);

    // JSON_HEX_* escapes <, >, & and quotes so a product name cannot close the <script> tag
    $flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE;
    $calls = '';
    foreach ($events as $queued) {
        $name = json_encode($queued['event'], $flags);
        $properties = json_encode((object) $queued['properties'], $flags);
        if ($name !== false && $properties !== false) {
            $calls .= "    window.pendo.track($name, $properties);\n";
        }
    }

    // Does nothing when the Pendo snippet is not loaded on the page
    echo "<script>\nif (window.pendo && typeof window.pendo.track === 'function') {\n$calls}\n</script>\n";
}
