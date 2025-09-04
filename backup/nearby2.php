<?php
header("Content-Type: application/json");

function logRequest($message) {
    $logFile = 'api_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] - $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

$apiKey = '88c40f69bbe84221ba7a2d661eb80a85';

$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;
$locationName = $_GET['location_name'] ?? null;
$radius = $_GET['radius'] ?? 1500;
$limit = $_GET['limit'] ?? 10;

if ((!$lat || !$lon) && $locationName) {
    // Geocode location_name using Geoapify
    $geocodeUrl = "https://api.geoapify.com/v1/geocode/search?" . http_build_query([
        'text' => $locationName,
        'format' => 'json',
        'apiKey' => $apiKey
    ]);

    logRequest("Geocoding location: $locationName | URL: $geocodeUrl");

    $geocodeResponse = file_get_contents($geocodeUrl);
    $geoData = json_decode($geocodeResponse, true);

    if (!empty($geoData['results'])) {
        $lat = $geoData['results'][0]['lat'];
        $lon = $geoData['results'][0]['lon'];
        logRequest("Resolved coordinates for '$locationName': lat=$lat, lon=$lon");
    } else {
        logRequest("ERROR: Unable to resolve location: $locationName");
        http_response_code(404);
        echo json_encode(["error" => "Unable to resolve location name"]);
        exit;
    }
}

if (!$lat || !$lon) {
    http_response_code(400);
    logRequest("ERROR: Missing coordinates and no valid location name");
    echo json_encode(["error" => "Missing latitude, longitude, or valid location_name"]);
    exit;
}

$url = "https://api.geoapify.com/v2/places?" . http_build_query([
    'categories' => 'catering.restaurant,catering.bar,catering.fast_food',
    'filter' => "circle:$lon,$lat,$radius",
    'bias' => "proximity:$lon,$lat",
    'lang' => 'en',
    'limit' => $limit,
    'apiKey' => $apiKey
]);

logRequest("Nearby Search URL: $url");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

logRequest("Response Code: $httpCode");

if ($httpCode !== 200) {
    http_response_code($httpCode);
    logRequest('ERROR: Failed to fetch places from Geoapify');
    echo json_encode(["error" => "Failed to fetch places"]);
    exit;
}

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

logRequest("Returned " . count($results) . " nearby places.");
echo json_encode(['results' => $results], JSON_PRETTY_PRINT);
?>
