<?php
/**
 * stats.php
 * 
 * API endpoint to retrieve analytics statistics
 * Returns all analytics data as JSON
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$analyticsFile = __DIR__ . '/analytics.json';

// If analytics file doesn't exist, return empty stats
if (!file_exists($analyticsFile)) {
    echo json_encode([
        'events' => [],
        'metadata' => [
            'created_at' => null,
            'last_updated' => null
        ],
        'summary' => [
            'total_events' => 0,
            'page_views' => 0,
            'cta_resume_clicks' => 0,
            'cta_waitlist_clicks' => 0,
            'pricing_clicks' => 0,
            'scroll_25' => 0,
            'scroll_50' => 0,
            'scroll_75' => 0,
            'scroll_100' => 0,
            'exit_no_interaction' => 0
        ]
    ]);
    exit;
}

// Read analytics file
$fileContent = file_get_contents($analyticsFile);
$analytics = json_decode($fileContent, true);

if ($analytics === null) {
    // If JSON is corrupted, return empty stats
    echo json_encode([
        'events' => [],
        'metadata' => [
            'created_at' => null,
            'last_updated' => null
        ],
        'summary' => [
            'total_events' => 0,
            'page_views' => 0,
            'cta_resume_clicks' => 0,
            'cta_waitlist_clicks' => 0,
            'pricing_clicks' => 0,
            'scroll_25' => 0,
            'scroll_50' => 0,
            'scroll_75' => 0,
            'scroll_100' => 0,
            'exit_no_interaction' => 0
        ]
    ]);
    exit;
}

// Calculate summary statistics
$events = isset($analytics['events']) ? $analytics['events'] : [];
$totalEvents = array_sum($events);

$summary = [
    'total_events' => $totalEvents,
    'page_views' => isset($events['page_view']) ? $events['page_view'] : 0,
    'cta_resume_clicks' => isset($events['cta_resume_click']) ? $events['cta_resume_click'] : 0,
    'cta_waitlist_clicks' => isset($events['cta_waitlist_click']) ? $events['cta_waitlist_click'] : 0,
    'pricing_clicks' => isset($events['pricing_click']) ? $events['pricing_click'] : 0,
    'scroll_25' => isset($events['scroll_25']) ? $events['scroll_25'] : 0,
    'scroll_50' => isset($events['scroll_50']) ? $events['scroll_50'] : 0,
    'scroll_75' => isset($events['scroll_75']) ? $events['scroll_75'] : 0,
    'scroll_100' => isset($events['scroll_100']) ? $events['scroll_100'] : 0,
    'exit_no_interaction' => isset($events['exit_no_interaction']) ? $events['exit_no_interaction'] : 0
];

// Add summary to response
$analytics['summary'] = $summary;

// Return complete analytics data
echo json_encode($analytics, JSON_PRETTY_PRINT);
?>

