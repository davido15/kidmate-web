<?php
// Generate password hash for admin user
$password = "password"; // Change this to your desired password
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";

// Test verification
if (password_verify($password, $hash)) {
    echo "✅ Hash verification successful!\n";
} else {
    echo "❌ Hash verification failed!\n";
}
?> 