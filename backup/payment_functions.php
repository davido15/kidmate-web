<?php
include "db.php"; // Include database connection

function generatePaymentLink($order_id, $item_name, $price, $title, $description, $validity) {
    global $conn;

    $base_url = "https://pay.example.com/";

    // Generate a unique transaction ID
    $transaction_id = uniqid();

    // Convert validity to expiration date
    $validity_map = [
        "24h" => "+1 day",
        "48h" => "+2 days",
        "72h" => "+3 days",
        "1w" => "+7 days",
        "1m" => "+30 days"
    ];

    // Default to 24 hours if validity is invalid
    $expiry_time = isset($validity_map[$validity]) ? strtotime($validity_map[$validity]) : strtotime("+1 day");
    $expires_at = date("Y-m-d H:i:s", $expiry_time);

    // Generate the full payment link
    $payment_link = $transaction_id;    // Insert the payment link into the `paylinks` table
    $stmt = $conn->prepare("
        INSERT INTO paylinks (order_id, item_name, price, title, description, validity, payment_link, expires_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        payment_link = VALUES(payment_link), expires_at = VALUES(expires_at)
    ");

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("isssssss", $order_id, $item_name, $price, $title, $description, $validity, $payment_link, $expires_at);

    if ($stmt->execute()) {
        return [
            "payment_link" => $payment_link,
            "expires_at" => $expires_at
        ];
    } else {
        die("Error inserting/updating paylink: " . $stmt->error);
    }
}
?>
