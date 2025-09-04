<?php
$ip = $_SERVER['REMOTE_ADDR'];
$cacheFile = __DIR__ . "/cache/{$ip}.json";

if (!file_exists($cacheFile)) {
    echo json_encode(['error' => 'No location stored for this user']);
    exit;
}

$location = json_decode(file_get_contents($cacheFile), true);
$lat = $location['lat'];
$lon = $location['lon'];

$apiKey = '88c40f69bbe84221ba7a2d661eb80a85';
$url = "https://api.geoapify.com/v2/places?categories=catering.restaurant,catering.bar,catering.fast_food&filter=circle:$lon,$lat,1000&bias=proximity:$lon,$lat&lang=en&limit=5&apiKey=$apiKey";

$response = file_get_contents($url);
echo $response;
