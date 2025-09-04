<?php
include 'db.php';

// Reset admin password
$email = 'admin1@school.com';
$new_password = 'password123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Check if admin user exists
$check_query = "SELECT * FROM users WHERE email = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing user
    $update_query = "UPDATE users SET password_hash = ? WHERE email = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ss", $hashed_password, $email);
    
    if ($update_stmt->execute()) {
        echo "✅ Password updated successfully for admin1@school.com<br>";
        echo "New password: password123<br>";
    } else {
        echo "❌ Error updating password: " . $conn->error . "<br>";
    }
} else {
    // Create new admin user
    $insert_query = "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $name = 'Admin User';
    $role = 'admin';
    $insert_stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
    
    if ($insert_stmt->execute()) {
        echo "✅ Admin user created successfully<br>";
        echo "Email: admin1@school.com<br>";
        echo "Password: password123<br>";
    } else {
        echo "❌ Error creating admin user: " . $conn->error . "<br>";
    }
}

// Also check admin_users table
$check_admin_query = "SELECT * FROM admin_users WHERE email = ?";
$check_admin_stmt = $conn->prepare($check_admin_query);
$check_admin_stmt->bind_param("s", $email);
$check_admin_stmt->execute();
$admin_result = $check_admin_stmt->get_result();

if ($admin_result->num_rows > 0) {
    // Update existing admin user
    $update_admin_query = "UPDATE admin_users SET password_hash = ? WHERE email = ?";
    $update_admin_stmt = $conn->prepare($update_admin_query);
    $update_admin_stmt->bind_param("ss", $hashed_password, $email);
    
    if ($update_admin_stmt->execute()) {
        echo "✅ Admin password updated in admin_users table<br>";
    } else {
        echo "❌ Error updating admin password: " . $conn->error . "<br>";
    }
} else {
    // Create new admin user in admin_users table
    $insert_admin_query = "INSERT INTO admin_users (username, email, password_hash, role) VALUES (?, ?, ?, ?)";
    $insert_admin_stmt = $conn->prepare($insert_admin_query);
    $username = 'admin1';
    $role = 'admin';
    $insert_admin_stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
    
    if ($insert_admin_stmt->execute()) {
        echo "✅ Admin user created in admin_users table<br>";
    } else {
        echo "❌ Error creating admin user: " . $conn->error . "<br>";
    }
}

echo "<br><strong>Login Credentials:</strong><br>";
echo "Email: admin1@school.com<br>";
echo "Password: password123<br>";
echo "<br><a href='login.php'>Go to Login Page</a>";
?> 