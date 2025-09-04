<?php
include "db.php"; // Database connection

if (!isset($_GET["orderid"])) {
    die("Error: Payment link is missing.");
}

$payment_link = $_GET["orderid"];

$stmt = $conn->prepare("
    SELECT item_name, price, title, description, validity, payment_link, expires_at, status
    FROM paylinks
    WHERE payment_link = ? 
    LIMIT 1
");
$stmt->bind_param("s", $payment_link);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Error: Payment details not found.");
}

$payment = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Link Created</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        .container-fluid {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        @media (min-width: 768px) {
            .container-fluid {
                flex-direction: row;
            }
        }

        .column {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px;
            flex: 1;
            width: 100%;
            min-height: 33vh;
        }

        @media (min-width: 768px) {
            .column {
                height: 100vh;
            }
        }

        .content {
            text-align: center;
            width: 100%;
        }

        .blue-bg {
            background-color: #0a0a0a;
            color: white;
        }

        .grey-bg {
            background-color: #f0f0f0;
        }

        .separator {
            border-top: 3px solid white;
            width: 80%;
            margin: 20px auto;
        }

        .footer-new {
            text-align: center;
            font-size: 0.9rem;
            padding: 15px 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">

        <!-- Payment Details (Top on mobile, left on desktop) -->
        <div class="col-md-4 column blue-bg order-1 order-md-1">
            <div class="content">
                <h3>Payment Details</h3>
                <div class="separator"></div>
                <p><strong>TrxID:</strong> <a href="<?= htmlspecialchars($payment["payment_link"]) ?>" class="text-white"><?= htmlspecialchars($payment["payment_link"]) ?></a></p>
                <p><strong>Title:</strong> <?= htmlspecialchars($payment["title"]) ?></p>
                <p><strong>Amount:</strong> $<?= number_format($payment["price"], 2) ?></p>
                <p><strong>Description:</strong> <?= htmlspecialchars($payment["description"]) ?></p>
                <p><strong>Valid Until:</strong> <?= htmlspecialchars($payment["expires_at"]) ?></p>
            </div>
            <footer class="footer-new">
                &copy; 2025 Pozy 
            </footer>
        </div>

        <!-- Payment Options (Middle on both mobile and desktop) -->
        <div class="col-md-4 column grey-bg order-2 order-md-2">
            <div class="content">
                <h3>Payment Options</h3>
                <div class="separator"></div>
                <form>
                    <div class="mb-3">
                        <label class="form-label">Mobile Money Network</label>
                        <select class="form-select">
                            <option value="mtn">MTN</option>
                            <option value="airtel">Airtel</option>
                            <option value="vodafone">Vodafone</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" class="form-control" placeholder="Enter your mobile number">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Proceed to Pay</button>
                </form>
            </div>
            <footer class="footer-new">
                Secure Payment Gateway
            </footer>
        </div>

        <!-- More Information (Bottom on mobile, right on desktop) -->
        <div class="col-md-4 column grey-bg order-3 order-md-3">
            <div class="content">
                <h3>More Information</h3>
                <p>Any additional details or instructions can go here.</p>
            </div>
            <footer class="footer-new">
                Need Help? Contact Support
            </footer>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
