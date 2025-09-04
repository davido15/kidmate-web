<?php
include "db.php";
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION["email"];

// Fetch school statistics
$stats = [];

// Total Students
$students_query = "SELECT COUNT(*) as total FROM kids";
$students_result = $conn->query($students_query);
$stats['total_students'] = $students_result->fetch_assoc()['total'];

// Total Parents
$parents_query = "SELECT COUNT(*) as total FROM parents";
$parents_result = $conn->query($parents_query);
$stats['total_parents'] = $parents_result->fetch_assoc()['total'];

// Total Classes
$classes_query = "SELECT COUNT(*) as total FROM classes WHERE is_active = 1";
$classes_result = $conn->query($classes_query);
$stats['total_classes'] = $classes_result->fetch_assoc()['total'];

// Total Subjects
$subjects_query = "SELECT COUNT(*) as total FROM subjects WHERE is_active = 1";
$subjects_result = $conn->query($subjects_query);
$stats['total_subjects'] = $subjects_result->fetch_assoc()['total'];

// Today's Attendance
$today = date('Y-m-d');
$attendance_query = "SELECT COUNT(*) as total FROM attendance WHERE date = ?";
$attendance_stmt = $conn->prepare($attendance_query);
if ($attendance_stmt) {
    $attendance_stmt->bind_param("s", $today);
    $attendance_stmt->execute();
    $stats['today_attendance'] = $attendance_stmt->get_result()->fetch_assoc()['total'];
    $attendance_stmt->close();
} else {
    $stats['today_attendance'] = 0;
}

// Present Today
$present_query = "SELECT COUNT(*) as total FROM attendance WHERE date = ? AND status = 'present'";
$present_stmt = $conn->prepare($present_query);
if ($present_stmt) {
    $present_stmt->bind_param("s", $today);
    $present_stmt->execute();
    $stats['present_today'] = $present_stmt->get_result()->fetch_assoc()['total'];
    $present_stmt->close();
} else {
    $stats['present_today'] = 0;
}

// Absent Today
$absent_query = "SELECT COUNT(*) as total FROM attendance WHERE date = ? AND status = 'absent'";
$absent_stmt = $conn->prepare($absent_query);
if ($absent_stmt) {
    $absent_stmt->bind_param("s", $today);
    $absent_stmt->execute();
    $stats['absent_today'] = $absent_stmt->get_result()->fetch_assoc()['total'];
    $absent_stmt->close();
} else {
    $stats['absent_today'] = 0;
}

// Total Grades Recorded
$grades_query = "SELECT COUNT(*) as total FROM grades";
$grades_result = $conn->query($grades_query);
$stats['total_grades'] = $grades_result->fetch_assoc()['total'];

// Recent Grades (last 7 days)
$recent_grades_query = "SELECT COUNT(*) as total FROM grades WHERE date_recorded >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
$recent_grades_result = $conn->query($recent_grades_query);
$stats['recent_grades'] = $recent_grades_result->fetch_assoc()['total'];

// Current Term
$current_term_query = "SELECT * FROM terms WHERE start_date <= CURDATE() AND end_date >= CURDATE() AND is_active = 1 LIMIT 1";
$current_term_result = $conn->query($current_term_query);
$current_term = $current_term_result->fetch_assoc();

// Attendance Rate (last 30 days)
$attendance_rate_query = "
    SELECT 
        COUNT(CASE WHEN status = 'present' THEN 1 END) as present_count,
        COUNT(*) as total_count
    FROM attendance 
    WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
";
$attendance_rate_result = $conn->query($attendance_rate_query);
if ($attendance_rate_result) {
    $attendance_rate_data = $attendance_rate_result->fetch_assoc();
    $stats['attendance_rate'] = $attendance_rate_data['total_count'] > 0 ? 
        round(($attendance_rate_data['present_count'] / $attendance_rate_data['total_count']) * 100, 1) : 0;
} else {
    $stats['attendance_rate'] = 0;
}

// Recent Activities (last 5 records)
$recent_activities = [];

// Recent attendance
$recent_attendance_query = "
    SELECT a.*, a.child_name as student_name 
    FROM attendance a 
    ORDER BY a.created_at DESC 
    LIMIT 5
";
$recent_attendance_result = $conn->query($recent_attendance_query);
if ($recent_attendance_result) {
    while ($row = $recent_attendance_result->fetch_assoc()) {
        $recent_activities[] = [
            'type' => 'attendance',
            'student' => $row['student_name'],
            'status' => $row['status'],
            'date' => $row['date'],
            'time' => $row['created_at']
        ];
    }
}

// Recent grades
$recent_grades_query = "
    SELECT g.*, k.name as student_name 
    FROM grades g 
    JOIN kids k ON g.kid_id = k.id 
    ORDER BY g.date_recorded DESC 
    LIMIT 5
";
$recent_grades_result = $conn->query($recent_grades_query);
if ($recent_grades_result) {
    while ($row = $recent_grades_result->fetch_assoc()) {
        $recent_activities[] = [
            'type' => 'grade',
            'student' => $row['student_name'],
            'subject' => $row['subject'],
            'grade' => $row['grade'],
            'date' => $row['date_recorded'],
            'time' => $row['date_recorded']
        ];
    }
}

// Sort activities by date
usort($recent_activities, function($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});

$recent_activities = array_slice($recent_activities, 0, 5);
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
                            <h3>School Dashboard</h3>
                            <p class="mb-2">Welcome to KidMate School Management System, <?php echo $user_id ?></p>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="breadcrumbs"><a href="#">Home </a><span><i
                                    class="ri-arrow-right-s-line"></i></span><a href="#">Dashboard</a></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- School Statistics Cards -->
                <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-lg bg-primary rounded">
                                        <i class="ri-user-line text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-1"><?php echo $stats['total_students']; ?></h4>
                                    <p class="mb-0 text-muted">Total Students</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-lg bg-success rounded">
                                        <i class="ri-parent-fill text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-1"><?php echo $stats['total_parents']; ?></h4>
                                    <p class="mb-0 text-muted">Total Parents</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-lg bg-warning rounded">
                                        <i class="ri-inbox-archive-fill text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-1"><?php echo $stats['total_classes']; ?></h4>
                                    <p class="mb-0 text-muted">Active Classes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-lg bg-info rounded">
                                        <i class="ri-keyboard-line text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-1"><?php echo $stats['total_subjects']; ?></h4>
                                    <p class="mb-0 text-muted">Active Subjects</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Attendance Overview -->
                <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Today's Attendance</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="total-balance">
                                        <p>Total Records</p>
                                        <h2><?php echo $stats['today_attendance']; ?></h2>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                    <div class="balance-stats active">
                                        <p>Present</p>
                                        <h3><?php echo $stats['present_today']; ?></h3>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                    <div class="balance-stats">
                                        <p>Absent</p>
                                        <h3><?php echo $stats['absent_today']; ?></h3>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                    <div class="balance-stats">
                                        <p>Attendance Rate</p>
                                        <h3><?php echo $stats['attendance_rate']; ?>%</h3>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                    <div class="balance-stats">
                                        <p>Total Grades</p>
                                        <h3><?php echo $stats['total_grades']; ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             
                <div class="col-xl-8 col-lg-7">
                    <div id="user-activity" class="card" data-aos="fade-up">
                       <div class="card-header">
                          <h4 class="card-title">School Overview</h4>
                       </div>
                       <div class="card-body">
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="text-center mb-4">
                                      <h5>Current Term</h5>
                                      <?php if ($current_term): ?>
                                          <h3 class="text-primary"><?php echo htmlspecialchars($current_term['term_name']); ?></h3>
                                          <p class="text-muted">
                                              <?php echo date('M d', strtotime($current_term['start_date'])); ?> - 
                                              <?php echo date('M d, Y', strtotime($current_term['end_date'])); ?>
                                          </p>
                                      <?php else: ?>
                                          <p class="text-muted">No active term</p>
                                      <?php endif; ?>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="text-center mb-4">
                                      <h5>Recent Grades (7 days)</h5>
                                      <h3 class="text-success"><?php echo $stats['recent_grades']; ?></h3>
                                      <p class="text-muted">New grade records</p>
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-12">
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
                 </div>
                 
                 <div class="col-xl-4 col-lg-5">
                    <div class="card">
                       <div class="card-header">
                          <h4 class="card-title">Recent Activities</h4>
                       </div>
                       <div class="card-body">
                          <div class="recent-activities">
                             <?php if (empty($recent_activities)): ?>
                                <p class="text-muted text-center">No recent activities</p>
                             <?php else: ?>
                                <ul class="list-unstyled">
                                   <?php foreach ($recent_activities as $activity): ?>
                                   <li class="mb-3">
                                      <div class="d-flex align-items-center">
                                         <div class="flex-shrink-0">
                                            <?php if ($activity['type'] == 'attendance'): ?>
                                               <div class="avatar avatar-sm bg-<?php echo $activity['status'] == 'present' ? 'success' : 'danger'; ?> rounded">
                                                  <i class="ri-user-line text-white"></i>
                                               </div>
                                            <?php else: ?>
                                               <div class="avatar avatar-sm bg-info rounded">
                                                  <i class="ri-keyboard-line text-white"></i>
                                               </div>
                                            <?php endif; ?>
                                         </div>
                                         <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">
                                               <?php echo htmlspecialchars($activity['student']); ?>
                                            </h6>
                                            <p class="mb-0 text-muted small">
                                               <?php if ($activity['type'] == 'attendance'): ?>
                                                  <?php echo ucfirst($activity['status']); ?> on <?php echo date('M d', strtotime($activity['date'])); ?>
                                               <?php else: ?>
                                                  Grade <?php echo htmlspecialchars($activity['grade']); ?> in <?php echo htmlspecialchars($activity['subject']); ?>
                                               <?php endif; ?>
                                            </p>
                                         </div>
                                      </div>
                                   </li>
                                   <?php endforeach; ?>
                                </ul>
                             <?php endif; ?>
                          </div>
                       </div>
                    </div>
                 </div>

                 <div class="col-xl-8 col-lg-7">
                    <div class="card">
                       <div class="card-header">
                          <h4 class="card-title">Attendance Statistics</h4>
                       </div>
                       <div class="card-body">
                          <div class="row">
                              <div class="col-md-4 text-center">
                                  <div class="mb-3">
                                      <h3 class="text-success"><?php echo $stats['present_today']; ?></h3>
                                      <p class="text-muted">Present Today</p>
                                  </div>
                              </div>
                              <div class="col-md-4 text-center">
                                  <div class="mb-3">
                                      <h3 class="text-danger"><?php echo $stats['absent_today']; ?></h3>
                                      <p class="text-muted">Absent Today</p>
                                  </div>
                              </div>
                              <div class="col-md-4 text-center">
                                  <div class="mb-3">
                                      <h3 class="text-primary"><?php echo $stats['attendance_rate']; ?>%</h3>
                                      <p class="text-muted">Attendance Rate (30 days)</p>
                                  </div>
                              </div>
                          </div>
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




