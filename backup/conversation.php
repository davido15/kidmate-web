

<?php

// API Request Configuration
$api_url = "https://api.elevenlabs.io/v1/convai/conversations/pJogidJq7xiFDbHKMUqF";
$api_key = "sk_b1f548a76a483942b3658f1948019e1ffb3a62f2616d5a41";

// Initialize cURL
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "xi-api-key: $api_key"
]);

// Execute API Request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Check if request was successful
if ($curl_error || $http_code !== 200 || empty($response)) {
    die("<p style='color:red;'>Failed to fetch conversation. Please try again later.</p>");
}

// Decode JSON Response
$chat_data = json_decode($response, true);

// Validate JSON structure
if (!$chat_data || !isset($chat_data['transcript'])) {
    die("<p style='color:red;'>Invalid response format. Please try again later.</p>");
}

// Extract messages and additional details
$messages = $chat_data['transcript'];
$status = $chat_data['status'] ?? 'Unknown';
$metadata = $chat_data['metadata'] ?? [];
$analysis = $chat_data['analysis'] ?? [];

?>

<!DOCTYPE html>
<html lang="en">


<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pozy</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/davido15/pozy-static@main/css/style.css">

    <style>
        .chat-container { max-width: 600px; margin: auto; }
        .chat-box { padding: 10px; border-radius: 2px; margin-bottom: 10px; }
        .agent { background-color: #f4f4f5; text-align: left; color:black;}
        .user { background-color:#fafafa; text-align: right; color:blue; }
        .extra-info { margin-top: 20px; padding: 10px; border-top: 2px solid #ddd; }
        .extra-info h3 { margin-bottom: 10px; }
    </style>

   </head>

<body class="dashboard">

<div id="preloader">
    <i>.</i>
    <i>.</i>
    <i>.</i>
</div>

<div id="main-wrapper">

    <div class="header">
    <div class="container">
       <div class="row">
          <div class="col-xxl-12">
             <div class="header-content">
                <div class="header-left">
                   <div class="brand-logo"><a class="mini-logo" href="index.html"><img src="images/logoi.png" alt="" width="40"></a></div>
                   <div class="search">
                      <form action="#">
                         <div class="input-group"><input type="text" class="form-control" placeholder="Search Here"><span class="input-group-text"><i class="ri-search-line"></i></span></div>
                      </form>
                   </div>
                </div>
                <div class="header-right">
                   <div class="dark-light-toggle"><span class="dark"><i class="ri-moon-line"></i></span><span class="light"><i class="ri-sun-line"></i></span></div>
                   <div class="nav-item dropdown notification dropdown">
                      <div data-toggle="dropdown" aria-haspopup="true" class="" aria-expanded="false">
                         <div class="notify-bell icon-menu"><span><i class="ri-notification-2-line"></i></span></div>
                      </div>
                      <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu notification-list dropdown-menu dropdown-menu-right">
                         <h4>Recent Notification</h4>
                         <div class="lists">
                            <a class="" href="index.html#">
                               <div class="d-flex align-items-center">
                                  <span class="me-3 icon success"><i class="ri-check-line"></i></span>
                                  <div>
                                     <p>Account created successfully</p>
                                     <span>2020-11-04 12:00:23</span>
                                  </div>
                               </div>
                            </a>
                            <a class="" href="index.html#">
                               <div class="d-flex align-items-center">
                                  <span class="me-3 icon fail"><i class="ri-close-line"></i></span>
                                  <div>
                                     <p>2FA verification failed</p>
                                     <span>2020-11-04 12:00:23</span>
                                  </div>
                               </div>
                            </a>
                            <a class="" href="index.html#">
                               <div class="d-flex align-items-center">
                                  <span class="me-3 icon success"><i class="ri-check-line"></i></span>
                                  <div>
                                     <p>Device confirmation completed</p>
                                     <span>2020-11-04 12:00:23</span>
                                  </div>
                               </div>
                            </a>
                            <a class="" href="index.html#">
                               <div class="d-flex align-items-center">
                                  <span class="me-3 icon pending"><i class="ri-question-mark"></i></span>
                                  <div>
                                     <p>Phone verification pending</p>
                                     <span>2020-11-04 12:00:23</span>
                                  </div>
                               </div>
                            </a>
                            <a href="notification.html">More<i class="ri-arrow-right-s-line"></i></a>
                         </div>
                      </div>
                   </div>
                   <div class="dropdown profile_log dropdown">
                      <div data-toggle="dropdown" aria-haspopup="true" class="" aria-expanded="false">
                         <div class="user icon-menu active"><span><i class="ri-user-line"></i></span></div>
                      </div>
                      <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu dropdown-menu-right">
                         <div class="user-email">
                            <div class="user">
                               <span class="thumb"><img src="images/profile/3.png" alt=""></span>
                               <div class="user-info">
                                  <h5>Jannatul Maowa</h5>
                                  <span>Pozy.inc@gmail.com</span>
                               </div>
                            </div>
                         </div>
                         <a class="dropdown-item" href="profile.html"><span><i class="ri-user-line"></i></span>Profile</a>
                         <a class="dropdown-item" href="balance.html"><span><i class="ri-wallet-line"></i></span>Balance</a>
                         <a class="dropdown-item" href="settings-profile.html"><span><i class="ri-settings-3-line"></i></span>Settings</a>
                         <a class="dropdown-item" href="settings-activity.html"><span><i class="ri-time-line"></i></span>Activity</a>
                         <a class="dropdown-item" href="lock.html"><span><i class="ri-lock-line"></i></span>Lock</a>
                         <a class="dropdown-item logout" href="signin.html"><i class="ri-logout-circle-line"></i>Logout</a>
                      </div>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>

 <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
         
             
            <div class="row">
                <div class="col-xl-12">
                <h2>Chat Transcript</h2>
    <div class="chat-container">
        <?php
        foreach ($messages as $chat) {
            if (!isset($chat['role']) || !isset($chat['message'])) continue;

            $role = htmlspecialchars($chat['role']);
            $message = htmlspecialchars($chat['message']);
            $class = ($role === 'agent') ? 'chat-box agent' : 'chat-box user';

            echo "<div class='$class'><strong>$role:</strong> $message</div>";
        }
        ?>
    </div>

    <div class="extra-info">
        <h3>Conversation Details</h3>
        <p><strong>Status:</strong> <?= htmlspecialchars($status); ?></p>

        <?php if (!empty($metadata)): ?>
            <h3>Metadata</h3>
            <ul>
                <?php foreach ($metadata as $key => $value): ?>
                    <li><strong><?= htmlspecialchars($key); ?>:</strong> <?= htmlspecialchars(json_encode($value)); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($analysis)): ?>
            <h3>Analysis</h3>
            <ul>
                <?php foreach ($analysis as $key => $value): ?>
                    <li><strong><?= htmlspecialchars($key); ?>:</strong> <?= htmlspecialchars(json_encode($value)); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
                </div>
            </div>
            
            


        </div>
    </div>

</div>



<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/vendor/jquery/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/twbs/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/scripts.js"></script>


</body>


<!-- Mirrored from Pozy-html.vercel.app/create-invoice.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 13 Mar 2025 16:49:11 GMT -->
</html>