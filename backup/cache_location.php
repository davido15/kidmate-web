<?php
$data = json_decode(file_get_contents('php://input'), true);
$lat = $data['lat'] ?? null;
$lon = $data['lon'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'];

if ($lat && $lon) {
    $cacheDir = __DIR__ . '/cache';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir);
    }

    $file = $cacheDir . "/location.json";
    $locationData = ['lat' => $lat, 'lon' => $lon, 'time' => time()];
    file_put_contents($file, json_encode($locationData));
    echo json_encode(['status' => 'saved']);
} else {
    echo json_encode(['error' => 'Invalid location data']);
}
