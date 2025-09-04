<?php
include 'db.php';

echo "<h2>Creating Test Pickup Journey</h2>";

if ($conn) {
    // Create a test pickup journey
    $pickup_id = 'test-journey-' . time();
    $child_id = 5; // Hanna Yay
    $parent_id = 1;
    $pickup_person_id = 'test-pickup-person-001';
    $status = 'pending';
    $dropoff_location = 'Test School';
    $dropoff_lat = 5.5600;
    $dropoff_lng = -0.2057;
    
    // First, let's check the table structure
    $check_query = "DESCRIBE pickup_journey";
    $check_result = mysqli_query($conn, $check_query);
    
    if ($check_result) {
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = mysqli_fetch_assoc($check_result)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Try a simpler insert
    $query = "INSERT INTO pickup_journey (pickup_id, child_id, parent_id, pickup_person_id, status, dropoff_location, dropoff_latitude, dropoff_longitude) 
              VALUES ('$pickup_id', '$child_id', '$parent_id', '$pickup_person_id', '$status', '$dropoff_location', $dropoff_lat, $dropoff_lng)";
    
    if (mysqli_query($conn, $query)) {
        echo "<p style='color: green;'>✅ Test pickup journey created successfully!</p>";
        echo "<p><strong>Pickup ID:</strong> $pickup_id</p>";
        echo "<p><strong>Child:</strong> Hanna Yay (ID: $child_id)</p>";
        echo "<p><strong>Parent ID:</strong> $parent_id</p>";
        echo "<p><strong>Status:</strong> $status</p>";
        
        echo "<hr>";
        echo "<h3>Test the OTP Verification:</h3>";
        echo "<p>Visit this link to test OTP:</p>";
        echo "<a href='http://localhost:8888/KidMate/verify.php?pickup_id=$pickup_id' target='_blank'>";
        echo "http://localhost:8888/KidMate/verify.php?pickup_id=$pickup_id";
        echo "</a>";
        
    } else {
        echo "<p style='color: red;'>❌ Failed to create test pickup journey: " . mysqli_error($conn) . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>Database connection failed.</p>";
}
?> 