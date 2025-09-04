<?php
// Paystack API details
require 'function.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$paystack_url = "https://api.paystack.co/transaction/initialize";
$secret_key = "sk_test_d7543a492364e058e159881177924ffa48872bc3";



// Create connection
$conn = get_db_connection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Receive POST data from frontend
$email = $_POST['email'];
$dataprofile = $_POST['amount'];

// Split the string into two parts based on the first occurrence of '-'
list($amount, $profile) = explode('-', $dataprofile, 2);

  $payment_id = uniqid('pay_', true);

// Payload for Paystack API
$fields = [
    'email' => $email,
    'amount' => $amount,
  
];

$fields_string = json_encode($fields);

// Initialize cURL for Paystack API call
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $paystack_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $secret_key",
    "Content-Type: application/json",
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute API call
$result = curl_exec($ch);

// Check if cURL request was successful
if ($result === false) {
    echo json_encode([
        'status' => false,
        'message' => 'cURL error: ' . curl_error($ch)
    ]);
    curl_close($ch);
    exit();
}

curl_close($ch);

// Decode the response
$response = json_decode($result, true);

// Check the response from Paystack
if ($response['status'] === true) {
    // Extract data for successful transaction initialization
    $authorization_url = $response['data']['authorization_url'];
    $reference = $response['data']['reference'];
    
    // Generate a unique payment ID (using Paystack reference as payment_id)
   // Using `uniqid()` to generate a unique payment ID
    
    // Insert transaction into the database
   
    // Insert transaction into the database
$stmt = $conn->prepare("INSERT INTO transactions (payment_id, email, amount, profile, reference, status) VALUES (?, ?, ?, ?, ?, ?)");
$status = 'pending'; // Default status is 'pending' until payment is verified

// Bind parameters and execute query
$stmt->bind_param("ssisss", $payment_id, $email, $amount, $profile, $reference, $status);

    if ($stmt->execute()) {
        // Return the authorization URL and reference as JSON
        echo json_encode([
            'status' => true,
            'authorization_url' => $authorization_url,
            'reference' => $reference,
            'payment_id' => $payment_id // Return the unique payment ID
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Error inserting transaction into the database: ' . $stmt->error
        ]);
    }
    
    $stmt->close();
} else {
    // Handle Paystack API errors
    echo json_encode([
        'status' => false,
        'message' => $response['message']
    ]);
}

// Close the database connection
$conn->close();

// Function to verify payment
function verifyPayment($reference)
{
    $paystack_verify_url = "https://api.paystack.co/transaction/verify/";
    $secret_key = "sk_test_d7543a492364e058e159881177924ffa48872bc3";

    // Check if the reference is provided
    if (!$reference) {
        return [
            'status' => false,
            'message' => 'Transaction reference is required.'
        ];
    }

    // Verify transaction with Paystack API
    $verify_url = $paystack_verify_url . $reference;

    // Initialize cURL for verification request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $verify_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $secret_key",
        "Content-Type: application/json",
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute API call
    $result = curl_exec($ch);

    // Check if cURL request was successful
    if ($result === false) {
        return [
            'status' => false,
            'message' => 'cURL error: ' . curl_error($ch)
        ];
    }

    curl_close($ch);

    // Decode the response
    $response = json_decode($result, true);

    // Check if the transaction is verified and successful
    if ($response['status'] === true && $response['data']['status'] === 'success') {
        // Payment verification success, return relevant data
        return [
            'status' => true,
            'message' => 'Transaction verified successfully.',
            'data' => $response['data']
        ];
    } else {
        // Transaction verification failed, return message
        return [
            'status' => false,
            'message' => $response['message'] ?? 'Transaction verification failed.',
            'data' => $response['data'] ?? null
        ];
    }
}
?>
