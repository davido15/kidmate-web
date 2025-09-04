<?php
header("Content-Type: application/json");
include "db.php"; // Database connection
include "payment_functions.php"; // Payment link generator
include "log.php"; // Logging function

// Telegram Bot Configuration
$TELEGRAM_BOT_TOKEN = "7076376293:AAFHe7F4_Lb2Gi-bxlNuHUwJ5kaf2gyo6Mo";
$TELEGRAM_CHAT_ID = "5056602137"; // Replace with your Telegram chat ID

$BASE_PAYMENT_URL = "https://outrankconsult.com/pozy/details.php?orderid=";

// Read JSON request data
$data = json_decode(file_get_contents("php://input"), true);

// Log received data
logMessage("Received request: " . json_encode($data));

// Validate request data
if (!isset($data["phone_number"]) || !isset($data["delivery_address"]) || !isset($data["order_item"]) || !isset($data["total_cost"])) {
    logMessage("Error: Missing required fields");
    echo json_encode(["status" => "error", "message" => "Invalid request data"]);
    exit;
}

$phone_number = $data["phone_number"];
$delivery_address = $data["delivery_address"];
$order_item = $data["order_item"];
$price = $data["total_cost"];
$title = isset($data["title"]) ? $data["title"] : $order_item;
$description = isset($data["description"]) ? $data["description"] : "Order for " . $order_item;
$validity = "24h"; // Default to 24 hours

// Insert order into database WITHOUT payment link initially
$stmt = $conn->prepare("INSERT INTO orders (phone_number, delivery_address, order_item, total_cost) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    logMessage("Error: Order insert statement preparation failed - " . $conn->error);
}
$stmt->bind_param("sssd", $phone_number, $delivery_address, $order_item, $price);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    logMessage("Order created successfully: Order ID - $order_id");

    // Generate payment link
    $paymentData = generatePaymentLink($order_id, $order_item, $price, $title, $description, $validity);
    $payment_link = $paymentData["payment_link"];
    $expires_at = $paymentData["expires_at"];

    // Update the order with the generated payment link
    $stmt2 = $conn->prepare("UPDATE orders SET payment_link = ? WHERE id = ?");
    if (!$stmt2) {
        logMessage("Error: Order update statement preparation failed - " . $conn->error);
    }
    $stmt2->bind_param("si", $payment_link, $order_id);

    if ($stmt2->execute()) {
        logMessage("Payment link updated for order: $payment_link");
    } else {
        logMessage("Error: Failed to update payment link - " . $stmt2->error);
    }

    $newpayment_link = $BASE_PAYMENT_URL . $payment_link; 
    // Send Telegram Notification
    $message = "🆕 *New Order Created!* \n\n"
             . "📱 Phone: $phone_number\n"
             . "📍 Address: $delivery_address\n"
             . "🍕 Order: $order_item\n"
             . "💰 Total Cost: $$price\n"
             . "🔗 Payment Link: $newpayment_link\n"
             . "⏳ Expires: $expires_at";

    $telegram_url = "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage?chat_id=$TELEGRAM_CHAT_ID&text=" . urlencode($message) . "&parse_mode=Markdown";
    $telegram_response = file_get_contents($telegram_url);
    
    if ($telegram_response) {
        logMessage("Telegram notification sent successfully.");
    } else {
        logMessage("Error: Failed to send Telegram notification.");
    }

    echo json_encode([
        "status" => "success",
        "order_id" => $order_id,
        "payment_link" => $newpayment_link,
        "expires_at" => $expires_at
    ]);
} else {
    logMessage("Error: Failed to create order - " . $stmt->error);
    echo json_encode(["status" => "error", "message" => "Failed to create order"]);
}
?>
