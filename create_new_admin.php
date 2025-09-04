<?php
include 'db.php';

// New admin credentials - CHANGE THESE AS NEEDED
$new_admin_email = "newadmin@kidmate.com";
$new_admin_username = "newadmin";
$new_admin_password = "admin123";
$new_admin_name = "New Administrator";
$new_admin_role = "admin";

// Hash the password
$hashed_password = password_hash($new_admin_password, PASSWORD_DEFAULT);

echo "<h2>🔧 Creating New Admin User</h2>";

// Check if admin user already exists in users table
$check_query = "SELECT * FROM users WHERE email = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("s", $new_admin_email);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    echo "❌ Admin user already exists in users table with email: $new_admin_email<br>";
} else {
    // Create new admin user in users table
    $insert_query = "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("ssss", $new_admin_name, $new_admin_email, $hashed_password, $new_admin_role);
    
    if ($insert_stmt->execute()) {
        echo "✅ New admin user created successfully in users table<br>";
        echo "Name: $new_admin_name<br>";
        echo "Email: $new_admin_email<br>";
        echo "Role: $new_admin_role<br>";
    } else {
        echo "❌ Error creating admin user in users table: " . $conn->error . "<br>";
    }
}

// Check if admin user already exists in admin_users table
$check_admin_query = "SELECT * FROM admin_users WHERE email = ?";
$check_admin_stmt = $conn->prepare($check_admin_query);
$check_admin_stmt->bind_param("s", $new_admin_email);
$check_admin_stmt->execute();
$admin_result = $check_admin_stmt->get_result();

if ($admin_result->num_rows > 0) {
    echo "❌ Admin user already exists in admin_users table with email: $new_admin_email<br>";
} else {
    // Create new admin user in admin_users table
    $insert_admin_query = "INSERT INTO admin_users (username, email, password_hash, role) VALUES (?, ?, ?, ?)";
    $insert_admin_stmt = $conn->prepare($insert_admin_query);
    $insert_admin_stmt->bind_param("ssss", $new_admin_username, $new_admin_email, $hashed_password, $new_admin_role);
    
    if ($insert_admin_stmt->execute()) {
        echo "✅ New admin user created successfully in admin_users table<br>";
        echo "Username: $new_admin_username<br>";
        echo "Email: $new_admin_email<br>";
        echo "Role: $new_admin_role<br>";
    } else {
        echo "❌ Error creating admin user in admin_users table: " . $conn->error . "<br>";
    }
}

echo "<br><strong>🎯 New Admin Login Credentials:</strong><br>";
echo "Email: $new_admin_email<br>";
echo "Password: $new_admin_password<br>";
echo "Username: $new_admin_username<br>";

echo "<br><strong>📋 All Admin Users:</strong><br>";

// List all admin users
$all_admins_query = "SELECT username, email, role FROM admin_users ORDER BY created_at DESC";
$all_admins_result = $conn->query($all_admins_query);

if ($all_admins_result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Username</th><th>Email</th><th>Role</th></tr>";
    while ($admin = $all_admins_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $admin['username'] . "</td>";
        echo "<td>" . $admin['email'] . "</td>";
        echo "<td>" . $admin['role'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No admin users found in admin_users table<br>";
}

echo "<br><a href='login.php'>🔐 Go to Login Page</a> | ";
echo "<a href='dashboard.php'>📊 Go to Dashboard</a>";

$conn->close();
?> 