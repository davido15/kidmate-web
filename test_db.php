<?php
include 'db.php';

echo "<h2>Database Connection Test</h2>";

// Test connection
if ($conn->ping()) {
    echo "✅ Database connection successful<br>";
} else {
    echo "❌ Database connection failed<br>";
    exit;
}

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "✅ Users table exists<br>";
    
    // Show table structure
    $result = $conn->query("DESCRIBE users");
    echo "<h3>Users table structure:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Count users
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();
    echo "<br>Total users: " . $row['count'] . "<br>";
    
} else {
    echo "❌ Users table does not exist<br>";
    
    // Create users table if it doesn't exist
    $sql = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(15) UNIQUE,
        password_hash VARCHAR(255),
        role VARCHAR(50) DEFAULT 'Parent',
        image VARCHAR(255),
        push_token VARCHAR(255)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Users table created successfully<br>";
    } else {
        echo "❌ Error creating table: " . $conn->error . "<br>";
    }
}

$conn->close();
?> 