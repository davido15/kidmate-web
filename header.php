<!DOCTYPE html>
<html lang="en">


<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KidMate</title>
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/davido15/pozy-static@main/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<link href="DataTables/datatables.min.css" rel="stylesheet">





<style>
  div.dataTables_filter input {
  width: 500px;
  margin: 10px;
}

/* Submenu Styles */
.has-submenu {
  position: relative;
}

.submenu {
  display: none;
  list-style: none;
  padding-left: 0;
  margin: 0;
  background: rgba(255, 255, 255, 0.05);
  border-left: 3px solid #007bff;
}

.submenu li {
  margin: 0;
}

.submenu li a {
  padding: 10px 20px 10px 50px;
  font-size: 14px;
  color: #b7c0cd;
  display: flex;
  align-items: center;
  text-decoration: none;
  transition: all 0.3s ease;
}

.submenu li a:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
}

.submenu li a span:first-child {
  margin-right: 10px;
  font-size: 16px;
}

.submenu-arrow {
  margin-left: auto;
  transition: transform 0.3s ease;
}

.submenu-arrow.active {
  transform: rotate(180deg);
}

.submenu.active {
  display: block;
}
</style>

<script>
function toggleSubmenu(submenuId) {
  const submenu = document.getElementById(submenuId);
  const arrow = event.currentTarget.querySelector('.submenu-arrow');
  
  // Close all other submenus
  const allSubmenus = document.querySelectorAll('.submenu');
  const allArrows = document.querySelectorAll('.submenu-arrow');
  
  allSubmenus.forEach(menu => {
    if (menu.id !== submenuId) {
      menu.classList.remove('active');
    }
  });
  
  allArrows.forEach(arrow => {
    if (arrow !== event.currentTarget.querySelector('.submenu-arrow')) {
      arrow.classList.remove('active');
    }
  });
  
  // Toggle current submenu
  submenu.classList.toggle('active');
  arrow.classList.toggle('active');
  
  // Prevent default link behavior
  event.preventDefault();
}

// Auto-expand submenu if current page is in academic section
document.addEventListener('DOMContentLoaded', function() {
  const currentPage = window.location.pathname.split('/').pop();
  const academicPages = ['manage_classes.php', 'manage_subjects.php', 'manage_terms.php'];
  
  if (academicPages.includes(currentPage)) {
    const submenu = document.getElementById('academic-submenu');
    const arrow = document.querySelector('.submenu-arrow');
    if (submenu && arrow) {
      submenu.classList.add('active');
      arrow.classList.add('active');
    }
  }
});
</script>
</head>

<body class="dashboard">

<div id="preloader">
    <i>.</i>
    <i>.</i>
    <i>.</i>
</div>