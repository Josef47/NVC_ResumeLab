<?php
/**
 * track.php
 * 
 * API endpoint to track user events and store them in analytics.json
 * Accepts POST requests with JSON payload: { "event": "event_name" }
 */

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!isset($data['event']) || empty($data['event'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Event name is required']);
    exit;
}

$eventName = $data['event'];
$analyticsFile = __DIR__ . '/analytics.json';

// Initialize analytics data structure if file doesn't exist
if (!file_exists($analyticsFile)) {
    $analytics = [
        'events' => [],
        'metadata' => [
            'created_at' => date('Y-m-d H:i:s'),
            'last_updated' => date('Y-m-d H:i:s')
        ]
    ];
} else {
    // Read existing analytics
    $fileContent = file_get_contents($analyticsFile);
    $analytics = json_decode($fileContent, true);
    
    if ($analytics === null) {
        // If JSON is corrupted, reset
        $analytics = [
            'events' => [],
            'metadata' => [
                'created_at' => date('Y-m-d H:i:s'),
                'last_updated' => date('Y-m-d H:i:s')
            ]
        ];
    }
}

// Initialize event counter if it doesn't exist
if (!isset($analytics['events'][$eventName])) {
    $analytics['events'][$eventName] = 0;
}

// Increment event counter
$analytics['events'][$eventName]++;
$analytics['metadata']['last_updated'] = date('Y-m-d H:i:s');

// Save to file
$result = file_put_contents($analyticsFile, json_encode($analytics, JSON_PRETTY_PRINT));

if ($result === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save analytics']);
    exit;
}

// Return success response
echo json_encode(['success' => true]);
?>

