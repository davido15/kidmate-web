<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from Pozy-html.vercel.app/bill.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 13 Mar 2025 16:48:49 GMT -->
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
            <div class="page-title">
               <div class="row align-items-center justify-content-between">
                  <div class="col-xl-4">
                     <div class="page-title-content">
                        <h3>Payments</h3>


                     </div>
                  </div>
                  <div class="col-auto">
                     <div class="breadcrumbs"><a href="#">Home </a><span><i class="ri-arrow-right-s-line"></i></span><a href="#">Payments</a></div>
                  </div>
               </div>
            </div>
            <div class="row">
             
              
               <div class="col-xl-12">
                  <div class="card">
                     <div class="card-header">
                        <h4 class="card-title">Payments</h4>
                     </div>
                     <div class="card-body">
                        <div class="payments-content">
                           <div class="table-responsive">
                              <table class="table">
                                 <thead>
                                    <tr>
                                       <th>
                                          <div class="form-check"><input class="form-check-input" type="checkbox" id="flexCheckDefault" value=""></div>
                                       </th>
                                       <th>Service</th>
                                       <th>Amount</th>
                                       <th>Date</th>
                                       <th>Status</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr>
                                       <td>
                                          <div class="form-check"><input class="form-check-input" type="checkbox" id="flexCheckDefault" value=""></div>
                                       </td>
                                       <td><img src="images/social/facebook.png" alt="" width="22" class="me-3 img-fluid">Facebook Ads</td>
                                       <td>$412</td>
                                       <td>March 21, 2021</td>
                                       <td><span class="badge px-3 py-2 bg-success">Paid</span></td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <div class="form-check"><input class="form-check-input" type="checkbox" id="flexCheckDefault" value=""></div>
                                       </td>
                                       <td><img src="images/social/youtube.png" alt="" width="22" class="me-3 img-fluid">Youtube Premium</td>
                                       <td>$175</td>
                                       <td>December 26, 2021</td>
                                       <td><span class="badge px-3 py-2 bg-danger">Cancel</span></td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <div class="form-check"><input class="form-check-input" type="checkbox" id="flexCheckDefault" value=""></div>
                                       </td>
                                       <td><img src="images/social/dropbox.png" alt="" width="22" class="me-3 img-fluid">Dropbox</td>
                                       <td>$521</td>
                                       <td>February 16, 2021</td>
                                       <td><span class="badge px-3 py-2 bg-success">Paid</span></td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <div class="form-check"><input class="form-check-input" type="checkbox" id="flexCheckDefault" value=""></div>
                                       </td>
                                       <td><img src="images/social/google-plus.png" alt="" width="22" class="me-3 img-fluid">Google Plus</td>
                                       <td>$125</td>
                                       <td>June 17, 2021</td>
                                       <td><span class="badge px-3 py-2 bg-warning">Due</span></td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <div class="form-check"><input class="form-check-input" type="checkbox" id="flexCheckDefault" value=""></div>
                                       </td>
                                       <td><img src="images/social/spotify.png" alt="" width="22" class="me-3 img-fluid">Spotify</td>
                                       <td>$521</td>
                                       <td>August 01, 2021</td>
                                       <td><span class="badge px-3 py-2 bg-success">Paid</span></td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <div class="form-check"><input class="form-check-input" type="checkbox" id="flexCheckDefault" value=""></div>
                                       </td>
                                       <td><img src="images/social/skype.png" alt="" width="25" class="me-3 img-fluid">Skype</td>
                                       <td>$234</td>
                                       <td>January 19, 2021</td>
                                       <td><span class="badge px-3 py-2 bg-warning">Due</span></td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
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


</html>