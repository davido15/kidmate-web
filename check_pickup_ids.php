<?php
include 'db.php';

echo "<h2>Available Pickup IDs in Database</h2>";

if ($conn) {
    // Check pickup_journey table
    $query = "SELECT pickup_id, child_id, parent_id, status, created_at FROM pickup_journey ORDER BY created_at DESC LIMIT 10";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<h3>Pickup Journey Records:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Pickup ID</th><th>Child ID</th><th>Parent ID</th><th>Status</th><th>Created</th></tr>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['pickup_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['child_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['parent_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No pickup journey records found.</p>";
    }
    
    // Check kids table
    $query2 = "SELECT id, name, parent_id FROM kids LIMIT 5";
    $result2 = mysqli_query($conn, $query2);
    
    if ($result2 && mysqli_num_rows($result2) > 0) {
        echo "<h3>Kids Records:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Parent ID</th></tr>";
        
        while ($row = mysqli_fetch_assoc($result2)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['parent_id']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check users table
    $query3 = "SELECT id, username, email FROM users LIMIT 5";
    $result3 = mysqli_query($conn, $query3);
    
    if ($result3 && mysqli_num_rows($result3) > 0) {
        echo "<h3>Users Records:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th></tr>";
        
        while ($row = mysqli_fetch_assoc($result3)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} else {
    echo "<p style='color: red;'>Database connection failed.</p>";
}
?> 