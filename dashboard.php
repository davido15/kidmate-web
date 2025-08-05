<?php
include "db.php";
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION["email"];

?>



<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from intez-html.vercel.app/dashboard.php by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 13 Mar 2025 16:48:49 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pozy</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/davido15/pozy-static@main/css/style.css">
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
                   <div class="brand-logo"><a class="mini-logo" href="dashboard.php"><img src="images/logoi.png" alt="" width="40"></a></div>
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
                            <a class="" href="dashboard.php#">
                               <div class="d-flex align-items-center">
                                  <span class="me-3 icon success"><i class="ri-check-line"></i></span>
                                  <div>
                                     <p>Account created successfully</p>
                                     <span>2020-11-04 12:00:23</span>
                                  </div>
                               </div>
                            </a>
                            <a class="" href="dashboard.php#">
                               <div class="d-flex align-items-center">
                                  <span class="me-3 icon fail"><i class="ri-close-line"></i></span>
                                  <div>
                                     <p>2FA verification failed</p>
                                     <span>2020-11-04 12:00:23</span>
                                  </div>
                               </div>
                            </a>
                            <a class="" href="dashboard.php#">
                               <div class="d-flex align-items-center">
                                  <span class="me-3 icon success"><i class="ri-check-line"></i></span>
                                  <div>
                                     <p>Device confirmation completed</p>
                                     <span>2020-11-04 12:00:23</span>
                                  </div>
                               </div>
                            </a>
                            <a class="" href="dashboard.php#">
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
                                  <span>pozy.inc@gmail.com</span>
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
            <div class="page-title">
                <div class="row align-items-center justify-content-between">
                    <div class="col-xl-4">
                        <div class="page-title-content">
                            <h3>Dashboard</h3>
                            <p class="mb-2">Welcome Pozy Dashboard , <?php echo $user_id ?></p>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="breadcrumbs"><a href="#">Home </a><span><i
                                    class="ri-arrow-right-s-line"></i></span><a href="#">Dashboard</a></div>
                    </div>
                </div>
            </div>
            <div class="row">
               
               
              
                <div class=" col-xxl-4 col-xl-4 col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Balance Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="total-balance">
                                        <p>Total Transaction</p>
                                        <h2>₵ 21,478</h2>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                    <div class="balance-stats active">
                                        <p>Last Month</p>
                                        <h3>₵.42,678</h3>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                    <div class="balance-stats">
                                        <p>Expenses</p>
                                        <h3>₵.1,798</h3>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                    <div class="balance-stats">
                                        <p>Taxes</p>
                                        <h3>₵ 1400</h3>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                    <div class="balance-stats">
                                        <p>Profits</p>
                                        <h3>₵65,478</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             
                <div class="col-xl-8 col-lg-7">
                    <div id="user-activity" class="card" data-aos="fade-up">
                       <div class="card-header">
                          <h4 class="card-title">Expenses</h4>
                       </div>
                       <div class="card-body">
                          <div class="tab-content" id="myTabContent">
                             <div class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand">
                                   <div class=""></div>
                                </div>
                                <div class="chartjs-size-monitor-shrink">
                                   <div class=""></div>
                                </div>
                             </div>
                             <canvas id="activityBar"></canvas>
                          </div>
                       </div>
                    </div>
                 </div>
                 
                 <div class="col-xl-4 col-lg-5">
                    <div class="card">
                       <div class="card-header">
                          <h4 class="card-title">Unpaid Bills</h4>
                       </div>
                       <div class="card-body">
                          <div class="unpaid-content">
                             <ul>
                                <li>
                                   <p class="mb-0">Service</p>
                                   <h5 class="mb-0">Payoneer</h5>
                                </li>
                                <li>
                                   <p class="mb-0">Issued</p>
                                   <h5 class="mb-0">March 17, 2021</h5>
                                </li>
                                <li>
                                   <p class="mb-0">Payment Due</p>
                                   <h5 class="mb-0">17 Days</h5>
                                </li>
                                <li>
                                   <p class="mb-0">Paid</p>
                                   <h5 class="mb-0">0.00</h5>
                                </li>
                                <li>
                                   <p class="mb-0">Amount to pay</p>
                                   <h5 class="mb-0">$ 532.69</h5>
                                </li>
                                <li>
                                   <p class="mb-0">Payment Method</p>
                                   <h5 class="mb-0">Paypal</h5>
                                </li>
                             </ul>
                          </div>
                       </div>
                    </div>
                 </div>

                 <div class="col-xl-8 col-lg-7">
                    <div class="card">
                       <div class="card-header">
                          <h4 class="card-title">Sales</h4>
                       </div>
                       <div class="card-body">
                          <div class="chartjs-size-monitor">
                             <div class="chartjs-size-monitor-expand">
                                <div class=""></div>
                             </div>
                             <div class="chartjs-size-monitor-shrink">
                                <div class=""></div>
                             </div>
                          </div>
                          <canvas id="transaction-graph" ></canvas>
                       </div>
                    </div>
                 </div>
               
            


            </div>
        </div>
    </div>



</div>








<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/vendor/jquery/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/twbs/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/vendor/chartjs/chartjs.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/plugins/chartjs-line-init.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/plugins/chartjs-donut.js"></script>
<script src="https://cdn.jsdelivr.net/gh/perfect-scrollbar/perfect-scrollbar@1.5.0/dist/perfect-scrollbar.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/plugins/perfect-scrollbar-init.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/vendor/circle-progress/circle-progress.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/plugins/circle-progress-init.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/plugins/chartjs-bar-init.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/plugins/chartjs-investment.js"></script>



</body>


<!-- Mirrored from intez-html.vercel.app/dashboard.php by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 13 Mar 2025 16:48:49 GMT -->
</html>




