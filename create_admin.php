<?php
include 'db.php';

// Check if admin_users table exists
$result = $conn->query("SHOW TABLES LIKE 'admin_users'");
if ($result->num_rows == 0) {
    // Create admin_users table
    $sql = "CREATE TABLE admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Admin users table created successfully<br>";
    } else {
        echo "❌ Error creating table: " . $conn->error . "<br>";
        exit;
    }
} else {
    echo "✅ Admin users table already exists<br>";
}

    // Check if admin user already exists
    $stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ?");
    $admin_email = "admin@kidmate.com";
    $stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Create admin user
    $password_hash = password_hash("password", PASSWORD_BCRYPT);
    $admin_username = "admin";
    $stmt = $conn->prepare("INSERT INTO admin_users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $admin_username, $admin_email, $password_hash);
    
    if ($stmt->execute()) {
        echo "✅ Admin user created successfully<br>";
        echo "Username: admin<br>";
        echo "Password: password<br>";
    } else {
        echo "❌ Error creating admin user: " . $stmt->error . "<br>";
    }
} else {
    echo "✅ Admin user already exists<br>";
}

$stmt->close();
$conn->close();

echo "<br><a href='login.php'>Go to Login</a>";
?> 