<?php
header("Content-Type: application/json");

function logRequest($message) {
    $logFile = 'api_log.txt'; // Log file path
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] - $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    logRequest('ERROR: Only GET requests are allowed.');
    echo json_encode(["error" => "Only GET requests are allowed."]);
    exit;
}

$locationFile = './cache/location.json';

if (!file_exists($locationFile)) {
    http_response_code(400);
    echo json_encode(["error" => "No location cache found"]);
    exit;
}

$location = json_decode(file_get_contents($locationFile), true);

$lat = $location['lat'] ?? null;
$lon = $location['lon'] ?? null;

$radius = isset($_GET['radius']) ? $_GET['radius'] : 1000; // in meters
$limit = isset($_GET['limit']) ? $_GET['limit'] : 5;

if (!$lat || !$lon) {
    http_response_code(400);
    logRequest('ERROR: Missing latitude or longitude.');
    echo json_encode(["error" => "Missing latitude or longitude."]);
    exit;
}

// Geoapify API Key
$apiKey = '88c40f69bbe84221ba7a2d661eb80a85';

// Build request URL
$url = "https://api.geoapify.com/v2/places?" . http_build_query([
    'categories' => 'catering.restaurant,catering.bar,catering.fast_food',
    'filter' => "circle:$lon,$lat,$radius",
    'bias' => "proximity:$lon,$lat",
    'lang' => 'en',
    'limit' => $limit,
    'apiKey' => $apiKey
]);

// Log the request URL
logRequest("Request URL: $url");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Log the response
logRequest("Response Code: $httpCode");

if ($httpCode !== 200) {
    http_response_code($httpCode);
    logRequest('ERROR: Failed to fetch data from Geoapify');
    echo json_encode(["error" => "Failed to fetch data from Geoapify"]);
    exit;
}

// Parse response
$data = json_decode($response, true);
$results = [];

foreach ($data['features'] as $place) {
    $properties = $place['properties'];
    $results[] = [
        'name' => $properties['name'] ?? 'Unknown',
        'address' => $properties['formatted'] ?? 'Not available',
        'lat' => $properties['lat'] ?? null,
        'lon' => $properties['lon'] ?? null,
        'phone' => $properties['phone'] ?? 'Not available',
        'website' => $properties['website'] ?? 'Not available',
        'opening_hours' => $properties['opening_hours'] ?? 'Not available',
    ];
}

// Log successful response
logRequest('Response sent successfully with ' . count($results) . ' results.');

echo json_encode(['results' => $results], JSON_PRETTY_PRINT);

?>
