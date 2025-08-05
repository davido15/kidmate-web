<?php
session_start();
include 'db.php'; // Your DB connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';
    
    // Check if we have the data
    if (empty($email) || empty($password)) {
        echo "❌ Email and password are required.";
        exit;
    }

    // Query for admin users
    $stmt = $conn->prepare("SELECT id, password_hash FROM admin_users WHERE email = ?");
    
    // Check if prepare statement failed
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $email);
    
    // Execute the statement
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    
    $stmt->store_result();

    // Debug: Show what we found
    echo "Looking for email: " . $email . "<br>";
    echo "Found " . $stmt->num_rows . " users<br>";

    // Check if user exists
    if ($stmt->num_rows > 0) {
        if (!$stmt->bind_result($user_id, $password_hash)) {
            die("Bind result failed: " . $stmt->error);
        }
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $password_hash)) {
            $_SESSION["email"] = $email ;// or $_SESSION["email"] = $email;
            header("Location: dashboard.php");
            exit();
        } else {
            echo "❌ Invalid email or password.";
        }
    } else {
        echo "❌ User not found.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">


<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KidMate</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <!-- Custom Stylesheet -->
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/davido15/pozy-static@main/css/style.css">

</head>

<body class="@@class">

<div id="preloader">
    <i>.</i>
    <i>.</i>
    <i>.</i>
</div>

<div class="authincation section-padding">
    <div class="container h-100">
        <div class="row justify-content-center h-100 align-items-center">
            <div class="col-xl-5 col-md-6">
                <div class="mini-logo text-center my-4">
                    <h3>
                       <a href="/index.php"><span style="color:  #121569;">Kid</span><span style="color: #020202;">Mate</span></a> 
                    </h3>


                    <h4 class="card-title mt-5">Sign in to KidMate</h4>
                </div>
                <div class="auth-form card">
                    <div class="card-body">
                        <form method="post"  class="signin_validate row g-3" action="?v=<?php echo time(); ?>" >
                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="hello@example.com" name="email" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" placeholder="Password" name="password" required>
                            </div>
                            <div class="col-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                    <label class="form-check-label" for="flexSwitchCheckDefault">Remember
                                        me</label>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <a href="reset.html">Forgot Password?</a>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                        <p class="mt-3 mb-0">Don't have an account? <a class="text-primary" href="register.php">Sign Up</a></p>
                    </div>

                </div>
                <div class="privacy-link">
                    <a href="signup.html">Have an issue with 2-factor
                        authentication?</a>
                    <br />
                    <a href="signup.html">Privacy Policy</a>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/vendor/jquery/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>


<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/scripts.js"></script>


</body>


</html>

