<?php
include 'db.php';

$email = 'admin1@school.com';

echo "<h2>🔍 Checking Admin User: admin1@school.com</h2>";

// Check users table
echo "<h3>📋 Users Table:</h3>";
$query = "SELECT id, name, email, role, password_hash FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "✅ User found in users table:<br>";
    echo "ID: " . $user['id'] . "<br>";
    echo "Name: " . $user['name'] . "<br>";
    echo "Email: " . $user['email'] . "<br>";
    echo "Role: " . $user['role'] . "<br>";
    echo "Password Hash: " . substr($user['password_hash'], 0, 20) . "...<br>";
} else {
    echo "❌ User not found in users table<br>";
}

// Check admin_users table
echo "<h3>👨‍💼 Admin Users Table:</h3>";
$admin_query = "SELECT id, username, email, role, password_hash FROM admin_users WHERE email = ?";
$admin_stmt = $conn->prepare($admin_query);
$admin_stmt->bind_param("s", $email);
$admin_stmt->execute();
$admin_result = $admin_stmt->get_result();

if ($admin_result->num_rows > 0) {
    $admin = $admin_result->fetch_assoc();
    echo "✅ Admin user found in admin_users table:<br>";
    echo "ID: " . $admin['id'] . "<br>";
    echo "Username: " . $admin['username'] . "<br>";
    echo "Email: " . $admin['email'] . "<br>";
    echo "Role: " . $admin['role'] . "<br>";
    echo "Password Hash: " . substr($admin['password_hash'], 0, 20) . "...<br>";
} else {
    echo "❌ Admin user not found in admin_users table<br>";
}

// Test password verification
echo "<h3>🔐 Password Test:</h3>";
$test_password = 'password123';

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($test_password, $user['password_hash'])) {
        echo "✅ Password 'password123' is correct for users table<br>";
    } else {
        echo "❌ Password 'password123' is incorrect for users table<br>";
    }
}

if ($admin_result->num_rows > 0) {
    $admin = $admin_result->fetch_assoc();
    if (password_verify($test_password, $admin['password_hash'])) {
        echo "✅ Password 'password123' is correct for admin_users table<br>";
    } else {
        echo "❌ Password 'password123' is incorrect for admin_users table<br>";
    }
}

echo "<br><a href='reset_admin_password.php'>Reset Admin Password</a> | ";
echo "<a href='login.php'>Go to Login</a>";
?> 