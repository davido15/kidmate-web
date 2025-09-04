<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Get sample pickup IDs from the database
$query = "SELECT DISTINCT pickup_id FROM pickup_journey ORDER BY timestamp DESC LIMIT 5";
$result = $conn->query($query);
$pickup_ids = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $pickup_ids[] = $row['pickup_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Verify Journey - KidMate</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/davido15/pozy-static@main/css/style.css">
    <style>
        .test-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .pickup-link {
            display: block;
            padding: 15px;
            margin: 10px 0;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            text-decoration: none;
            color: #007bff;
            transition: all 0.3s ease;
        }
        .pickup-link:hover {
            background: #e9ecef;
            border-color: #007bff;
            text-decoration: none;
            color: #0056b3;
        }
        .qr-demo {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
    </style>
</head>
<body class="dashboard">
    <?php include 'header.php'; ?>
    
    <div class="main-content">
        <?php include 'sidebar.php'; ?>
        
        <div class="content">
            <div class="test-container">
                <h2><i class="ri-qr-code-line"></i> Test Journey Verification</h2>
                
                <div class="qr-demo">
                    <h4>QR Code Demo</h4>
                    <p>When a QR code is scanned, it would contain a URL like:</p>
                    <code>https://yourdomain.com/verify.php?pickup_id=JWT0G3U3</code>
                </div>
                
                <h4>Test with Sample Pickup IDs:</h4>
                
                <?php if (!empty($pickup_ids)): ?>
                    <?php foreach ($pickup_ids as $pickup_id): ?>
                        <a href="verify.php?pickup_id=<?php echo urlencode($pickup_id); ?>" class="pickup-link">
                            <i class="ri-arrow-right-line"></i>
                            Test Journey: <strong><?php echo htmlspecialchars($pickup_id); ?></strong>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <p>No pickup journeys found in the database.</p>
                        <p>You can test with a sample pickup ID: <code>JWT0G3U3</code></p>
                        <a href="verify.php?pickup_id=JWT0G3U3" class="btn btn-primary">
                            Test with Sample ID
                        </a>
                    </div>
                <?php endif; ?>
                
                <hr>
                
                <h4>How it works:</h4>
                <ol>
                    <li>QR code contains the pickup ID as a URL parameter</li>
                    <li>Page loads with the pickup ID</li>
                    <li>System sends OTP to parent's registered email</li>
                    <li>User enters OTP to verify identity</li>
                    <li>Journey information is displayed after successful verification</li>
                </ol>
                
                <div class="text-center mt-4">
                    <a href="dashboard.php" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 