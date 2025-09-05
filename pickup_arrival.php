<?php
include 'db.php';
include 'email_service.php';

$pickup_id = $_GET['pickup_id'] ?? '';
$error_message = '';
$success_message = '';
$journey_data = null;
$location_data = null;

// Handle arrival confirmation
if (isset($_POST['action']) && $_POST['action'] == 'confirm_arrival' && !empty($pickup_id)) {
    $current_lat = $_POST['current_lat'] ?? '';
    $current_lng = $_POST['current_lng'] ?? '';
    $accuracy = $_POST['accuracy'] ?? '';
    
    if (empty($current_lat) || empty($current_lng)) {
        $error_message = "Location data is required to confirm arrival.";
    } else {
        // Get journey information including dropoff coordinates
        $query = "SELECT pj.*, k.name as child_name, k.image as child_image, 
                         pp.name as pickup_person_name, pp.image as pickup_person_image,
                         u.email as parent_email, u.name as parent_name,
                         pj.dropoff_latitude, pj.dropoff_longitude, pj.dropoff_location
                   FROM pickup_journey pj
                   LEFT JOIN kids k ON pj.child_id = k.id
                   LEFT JOIN pickup_persons pp ON pj.pickup_person_id = pp.uuid
                   LEFT JOIN users u ON pj.parent_id = u.id
                   WHERE pj.pickup_id = ?
                   ORDER BY pj.timestamp DESC
                   LIMIT 1";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $pickup_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $journey_data = $result->fetch_assoc();
            
            // Get dropoff coordinates from database
            $dropoff_lat = $journey_data['dropoff_latitude'] ?? null;
            $dropoff_lng = $journey_data['dropoff_longitude'] ?? null;
            
            // Check if coordinates are available
            if (empty($dropoff_lat) || empty($dropoff_lng)) {
                $error_message = "Drop-off location coordinates are not available for this journey.";
            } else {
                $distance = calculateDistance($current_lat, $current_lng, $dropoff_lat, $dropoff_lng);
                
                // Allow arrival if within 500 meters (0.5 km) of drop-off location
                if ($distance <= 0.5) {
                    // Insert new journey record with 'arrived' status, preserving all existing data
                    $arrival_query = "INSERT INTO pickup_journey (pickup_id, parent_id, child_id, pickup_person_id, status, timestamp, dropoff_location, dropoff_latitude, dropoff_longitude) 
                                     SELECT pickup_id, parent_id, child_id, pickup_person_id, 'arrived', NOW(), dropoff_location, dropoff_latitude, dropoff_longitude
                                     FROM pickup_journey 
                                     WHERE pickup_id = ? 
                                     ORDER BY timestamp DESC 
                                     LIMIT 1";
                    
                    $arrival_stmt = $conn->prepare($arrival_query);
                    $arrival_stmt->bind_param("s", $pickup_id);
                    
                    if ($arrival_stmt->execute()) {
                        $success_message = "Arrival confirmed! Distance from drop-off: " . round($distance * 1000) . " meters";
                        
                        // Send journey status notification email to parent
                        if (!empty($journey_data['parent_email']) && !empty($journey_data['parent_name']) && !empty($journey_data['child_name'])) {
                            $emailService = new EmailService();
                            $current_time = date('Y-m-d H:i:s');
                            $pickup_person_name = $journey_data['pickup_person_name'] ?? 'Pickup Person';
                            
                            $emailService->sendJourneyStatusNotification(
                                $journey_data['parent_email'],
                                $journey_data['parent_name'],
                                $journey_data['child_name'],
                                $pickup_person_name,
                                'arrived',
                                $current_time
                            );
                            
                            // Also send notification to daviddors12@gmail.com for monitoring
                            $emailService->sendJourneyStatusNotification(
                                "daviddors12@gmail.com",
                                "Admin",
                                $journey_data['child_name'],
                                $pickup_person_name,
                                'arrived',
                                $current_time,
                                "Parent: " . $journey_data['parent_name'] . " (" . $journey_data['parent_email'] . ")"
                            );
                        }
                        
                        // Store location data for display
                        $location_data = [
                            'current_lat' => $current_lat,
                            'current_lng' => $current_lng,
                            'dropoff_lat' => $dropoff_lat,
                            'dropoff_lng' => $dropoff_lng,
                            'distance' => $distance,
                            'accuracy' => $accuracy
                        ];
                        
                        // Refresh journey data
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            $journey_data = $result->fetch_assoc();
                        }
                    } else {
                        $error_message = "Failed to confirm arrival. Please try again.";
                    }
                } else {
                    $error_message = "You are too far from the drop-off location. Distance: " . round($distance * 1000) . " meters. Please get closer.";
                }
            }
        } else {
            $error_message = "Journey not found.";
        }
    }
}

// Function to calculate distance between two points using Haversine formula
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Earth's radius in kilometers
    
    $latDelta = deg2rad($lat2 - $lat1);
    $lonDelta = deg2rad($lon2 - $lon1);
    
    $a = sin($latDelta / 2) * sin($latDelta / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($lonDelta / 2) * sin($lonDelta / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
    return $earthRadius * $c;
}

// Get journey data for display
if (empty($journey_data) && !empty($pickup_id)) {
    $query = "SELECT pj.*, k.name as child_name, k.image as child_image, 
                     pp.name as pickup_person_name, pp.image as pickup_person_image,
                     u.email as parent_email, u.name as parent_name,
                     pj.dropoff_latitude, pj.dropoff_longitude, pj.dropoff_location
               FROM pickup_journey pj
               LEFT JOIN kids k ON pj.child_id = k.id
               LEFT JOIN pickup_persons pp ON pj.pickup_person_id = pp.uuid
               LEFT JOIN users u ON pj.parent_id = u.id
               WHERE pj.pickup_id = ?
               ORDER BY pj.timestamp DESC
               LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $pickup_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $journey_data = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickup Arrival - KidMate</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/davido15/pozy-static@main/css/style.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=geometry"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }
        .main-content {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        .content {
            padding: 20px;
        }
        .arrival-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .journey-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
            color: #333;
        }
        .info-value {
            color: #666;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-departed { background: #f8d7da; color: #721c24; }
        .status-picked { background: #d1ecf1; color: #0c5460; }
        .status-arrived { background: #d4edda; color: #155724; }
        .status-completed { background: #cce5ff; color: #004085; }
        .map-container {
            height: 400px;
            border-radius: 8px;
            overflow: hidden;
            margin: 20px 0;
            border: 2px solid #007bff;
        }
        .location-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #1e7e34;
        }
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background: #e0a800;
        }
        .btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
        .child-image, .picker-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .distance-info {
            background: #fff3cd;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="content">
            <div class="arrival-container">
                <h2><i class="ri-map-pin-line"></i> Pickup Arrival Confirmation</h2>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if (empty($pickup_id)): ?>
                    <div class="alert alert-warning">
                        <h4>No Pickup ID Provided</h4>
                        <p>Please use a valid link to access arrival confirmation.</p>
                    </div>
                <?php elseif (!$journey_data): ?>
                    <div class="alert alert-danger">
                        <h4>Journey Not Found</h4>
                        <p>No journey found with the provided pickup ID.</p>
                    </div>
                <?php else: ?>
                    <!-- Journey Information -->
                    <div class="journey-info">
                        <h4><i class="ri-route-line"></i> Journey Information</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="ri-user-heart-line"></i> Child Information</h5>
                                <div class="info-row">
                                    <span class="info-label">Name:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($journey_data['child_name']); ?></span>
                                </div>
                                <?php if (!empty($journey_data['child_image'])): ?>
                                    <div class="text-center mb-3">
                                        <img src="<?php echo htmlspecialchars($journey_data['child_image']); ?>" 
                                             alt="Child" class="child-image">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6">
                                <h5><i class="ri-user-star-line"></i> Pickup Person</h5>
                                <div class="info-row">
                                    <span class="info-label">Name:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($journey_data['pickup_person_name']); ?></span>
                                </div>
                                <?php if (!empty($journey_data['pickup_person_image'])): ?>
                                    <div class="text-center mb-3">
                                        <img src="<?php echo htmlspecialchars($journey_data['pickup_person_image']); ?>" 
                                             alt="Pickup Person" class="picker-image">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5><i class="ri-time-line"></i> Journey Details</h5>
                        <div class="info-row">
                            <span class="info-label">Pickup ID:</span>
                            <span class="info-value"><?php echo htmlspecialchars($journey_data['pickup_id']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                <span class="status-badge status-<?php echo $journey_data['status']; ?>">
                                    <?php echo ucfirst($journey_data['status']); ?>
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Drop-off Location:</span>
                            <span class="info-value"><?php echo htmlspecialchars($journey_data['dropoff_location'] ?? 'Location not specified'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Drop-off Coordinates:</span>
                            <span class="info-value">
                                <?php if (!empty($journey_data['dropoff_latitude']) && !empty($journey_data['dropoff_longitude'])): ?>
                                    <?php echo $journey_data['dropoff_latitude']; ?>, <?php echo $journey_data['dropoff_longitude']; ?>
                                <?php else: ?>
                                    Coordinates not available
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Map Container -->
                    <div class="map-container" id="map"></div>
                    
                    <!-- Location Information -->
                    <div class="location-info" id="locationInfo" style="display: none;">
                        <h5><i class="ri-map-pin-line"></i> Location Information</h5>
                        <div id="locationDetails"></div>
                        <div id="distanceInfo" class="distance-info"></div>
                    </div>
                    
                    <!-- Location and Arrival Buttons -->
                    <?php if ($journey_data['status'] == 'picked'): ?>
                        <div class="text-center">
                            <button id="getLocationBtn" class="btn btn-primary" onclick="getCurrentLocation()">
                                <i class="ri-map-pin-line"></i> Get My Location
                            </button>
                            <button id="showLocationBtn" class="btn btn-warning" onclick="showCurrentLocationOnMap()" style="display: none;">
                                <i class="ri-eye-line"></i> Show My Location on Map
                            </button>
                            <button id="confirmArrivalBtn" class="btn btn-success" onclick="confirmArrival()" disabled>
                                <i class="ri-check-line"></i> Confirm Arrival
                            </button>
                        </div>
                    <?php elseif ($journey_data['status'] == 'arrived'): ?>
                        <div class="alert alert-success">
                            <h4>Arrival Confirmed!</h4>
                            <p>The pickup person has confirmed arrival at the drop-off location.</p>
                        </div>
                    <?php elseif ($journey_data['status'] == 'completed'): ?>
                        <div class="alert alert-info">
                            <h4>Journey Completed!</h4>
                            <p>This journey has been completed successfully.</p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <h4>Cannot Confirm Arrival</h4>
                            <p>Arrival can only be confirmed when the journey status is 'Picked Up'.</p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Show Location Button for All Statuses -->
                    <div class="text-center" style="margin-top: 20px;">
                        <button id="showLocationBtnAll" class="btn btn-info" onclick="showCurrentLocationOnMap()">
                            <i class="ri-map-pin-line"></i> Show My Current Location on Map
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        let map;
        let currentLocationMarker;
        let dropoffLocationMarker;
        let currentLat = null;
        let currentLng = null;
        // Get dropoff coordinates from PHP/DB
        let dropoffLat = <?php echo !empty($journey_data['dropoff_latitude']) ? $journey_data['dropoff_latitude'] : 'null'; ?>;
        let dropoffLng = <?php echo !empty($journey_data['dropoff_longitude']) ? $journey_data['dropoff_longitude'] : 'null'; ?>;
        
        // Initialize map
        function initMap() {
            const defaultLocation = { lat: 0, lng: 0 };
            
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: defaultLocation,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            
            // Add dropoff location marker if available
            if (dropoffLat && dropoffLng) {
                dropoffLocationMarker = new google.maps.Marker({
                    position: { lat: parseFloat(dropoffLat), lng: parseFloat(dropoffLng) },
                    map: map,
                    title: 'Drop-off Location',
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 10,
                        fillColor: '#FF0000',
                        fillOpacity: 0.8,
                        strokeColor: '#FF0000',
                        strokeWeight: 2
                    }
                });
                
                // Center map on dropoff location
                map.setCenter({ lat: parseFloat(dropoffLat), lng: parseFloat(dropoffLng) });
                
                // Add info window for dropoff location
                const dropoffInfoWindow = new google.maps.InfoWindow({
                    content: '<div><strong>Drop-off Location</strong><br><?php echo htmlspecialchars($journey_data['dropoff_location'] ?? 'Location'); ?></div>'
                });
                
                dropoffLocationMarker.addListener('click', () => {
                    dropoffInfoWindow.open(map, dropoffLocationMarker);
                });
            } else {
                // Show message if no coordinates available
                const infoWindow = new google.maps.InfoWindow({
                    content: '<div><strong>No Drop-off Location Available</strong><br>Coordinates not found in database</div>'
                });
                infoWindow.open(map);
            }
        }
        
        // Get current location
        function getCurrentLocation() {
            const btn = document.getElementById('getLocationBtn');
            btn.innerHTML = '<i class="ri-loader-4-line"></i> Getting Location...';
            btn.disabled = true;
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        currentLat = position.coords.latitude;
                        currentLng = position.coords.longitude;
                        const accuracy = position.coords.accuracy;
                        
                        // Add current location marker
                        if (currentLocationMarker) {
                            currentLocationMarker.setMap(null);
                        }
                        
                        currentLocationMarker = new google.maps.Marker({
                            position: { lat: currentLat, lng: currentLng },
                            map: map,
                            title: 'Your Current Location',
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 8,
                                fillColor: '#007bff',
                                fillOpacity: 0.8,
                                strokeColor: '#007bff',
                                strokeWeight: 2
                            }
                        });
                        
                        // Add accuracy circle
                        const accuracyCircle = new google.maps.Circle({
                            strokeColor: '#007bff',
                            strokeOpacity: 0.3,
                            strokeWeight: 1,
                            fillColor: '#007bff',
                            fillOpacity: 0.1,
                            map: map,
                            center: { lat: currentLat, lng: currentLng },
                            radius: accuracy
                        });
                        
                        // Calculate distance if dropoff location is available
                        if (dropoffLat && dropoffLng) {
                            const distance = google.maps.geometry.spherical.computeDistanceBetween(
                                new google.maps.LatLng(currentLat, currentLng),
                                new google.maps.LatLng(dropoffLat, dropoffLng)
                            );
                            
                            const distanceKm = distance / 1000;
                            
                            // Show location info
                            document.getElementById('locationInfo').style.display = 'block';
                            document.getElementById('locationDetails').innerHTML = `
                                <strong>Your Location:</strong> ${currentLat.toFixed(6)}, ${currentLng.toFixed(6)}<br>
                                <strong>Accuracy:</strong> ${Math.round(accuracy)} meters<br>
                                <strong>Distance to Drop-off:</strong> ${distanceKm.toFixed(3)} km (${Math.round(distance)} meters)
                            `;
                            
                            // Enable/disable arrival button based on distance
                            const arrivalBtn = document.getElementById('confirmArrivalBtn');
                            if (distanceKm <= 0.5) { // Within 500 meters
                                arrivalBtn.disabled = false;
                                document.getElementById('distanceInfo').innerHTML = '✅ You are within range to confirm arrival!';
                                document.getElementById('distanceInfo').style.background = '#d4edda';
                                document.getElementById('distanceInfo').style.color = '#155724';
                            } else {
                                arrivalBtn.disabled = true;
                                document.getElementById('distanceInfo').innerHTML = '❌ You are too far from the drop-off location. Please get closer.';
                                document.getElementById('distanceInfo').style.background = '#f8d7da';
                                document.getElementById('distanceInfo').style.color = '#721c24';
                            }
                        } else {
                            // Show error if no dropoff coordinates
                            document.getElementById('locationInfo').style.display = 'block';
                            document.getElementById('locationDetails').innerHTML = `
                                <strong>Your Location:</strong> ${currentLat.toFixed(6)}, ${currentLng.toFixed(6)}<br>
                                <strong>Accuracy:</strong> ${Math.round(accuracy)} meters<br>
                                <strong>Drop-off Location:</strong> Not available
                            `;
                            document.getElementById('distanceInfo').innerHTML = '❌ Drop-off coordinates not available in database';
                            document.getElementById('distanceInfo').style.background = '#f8d7da';
                            document.getElementById('distanceInfo').style.color = '#721c24';
                        }
                        
                        // Center map to show both markers
                        const bounds = new google.maps.LatLngBounds();
                        bounds.extend({ lat: currentLat, lng: currentLng });
                        if (dropoffLat && dropoffLng) {
                            bounds.extend({ lat: parseFloat(dropoffLat), lng: parseFloat(dropoffLng) });
                        }
                        map.fitBounds(bounds);
                        
                        btn.innerHTML = '<i class="ri-map-pin-line"></i> Location Updated';
                        btn.style.background = '#28a745';
                    },
                    (error) => {
                        console.error('Error getting location:', error);
                        btn.innerHTML = '<i class="ri-error-warning-line"></i> Location Error';
                        btn.style.background = '#dc3545';
                        
                        alert('Error getting your location: ' + error.message);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            } else {
                alert('Geolocation is not supported by this browser.');
                btn.innerHTML = '<i class="ri-map-pin-line"></i> Get My Location';
                btn.disabled = false;
            }
        }
        
        // Show current location on map
        function showCurrentLocationOnMap() {
            const btn = document.getElementById('showLocationBtnAll');
            btn.innerHTML = '<i class="ri-loader-4-line"></i> Getting Location...';
            btn.disabled = true;
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const accuracy = position.coords.accuracy;
                        
                        // Remove existing current location marker
                        if (currentLocationMarker) {
                            currentLocationMarker.setMap(null);
                        }
                        
                        // Add current location marker
                        currentLocationMarker = new google.maps.Marker({
                            position: { lat: lat, lng: lng },
                            map: map,
                            title: 'Your Current Location',
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 8,
                                fillColor: '#007bff',
                                fillOpacity: 0.8,
                                strokeColor: '#007bff',
                                strokeWeight: 2
                            }
                        });
                        
                        // Add accuracy circle
                        const accuracyCircle = new google.maps.Circle({
                            strokeColor: '#007bff',
                            strokeOpacity: 0.3,
                            strokeWeight: 1,
                            fillColor: '#007bff',
                            fillOpacity: 0.1,
                            map: map,
                            center: { lat: lat, lng: lng },
                            radius: accuracy
                        });
                        
                        // Add info window for current location
                        const currentLocationInfoWindow = new google.maps.InfoWindow({
                            content: `
                                <div>
                                    <strong>Your Current Location</strong><br>
                                    Lat: ${lat.toFixed(6)}<br>
                                    Lng: ${lng.toFixed(6)}<br>
                                    Accuracy: ${Math.round(accuracy)} meters
                                </div>
                            `
                        });
                        
                        currentLocationMarker.addListener('click', () => {
                            currentLocationInfoWindow.open(map, currentLocationMarker);
                        });
                        
                        // Center map to show both markers
                        const bounds = new google.maps.LatLngBounds();
                        bounds.extend({ lat: lat, lng: lng });
                        if (dropoffLat && dropoffLng) {
                            bounds.extend({ lat: parseFloat(dropoffLat), lng: parseFloat(dropoffLng) });
                        }
                        map.fitBounds(bounds);
                        
                        // Show success message
                        alert(`Location captured!\nLatitude: ${lat.toFixed(6)}\nLongitude: ${lng.toFixed(6)}\nAccuracy: ${Math.round(accuracy)} meters`);
                        
                        btn.innerHTML = '<i class="ri-map-pin-line"></i> Show My Current Location on Map';
                        btn.disabled = false;
                    },
                    (error) => {
                        console.error('Error getting location:', error);
                        alert('Error getting your location: ' + error.message);
                        btn.innerHTML = '<i class="ri-map-pin-line"></i> Show My Current Location on Map';
                        btn.disabled = false;
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            } else {
                alert('Geolocation is not supported by this browser.');
                btn.innerHTML = '<i class="ri-map-pin-line"></i> Show My Current Location on Map';
                btn.disabled = false;
            }
        }
        
        // Confirm arrival
        function confirmArrival() {
            if (!currentLat || !currentLng) {
                alert('Please get your location first.');
                return;
            }
            
            if (!dropoffLat || !dropoffLng) {
                alert('Drop-off coordinates are not available.');
                return;
            }
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="confirm_arrival">
                <input type="hidden" name="current_lat" value="${currentLat}">
                <input type="hidden" name="current_lng" value="${currentLng}">
                <input type="hidden" name="accuracy" value="10">
            `;
            document.body.appendChild(form);
            form.submit();
        }
        
        // Initialize map when page loads
        window.onload = function() {
            initMap();
        };
    </script>
</body>
</html> 