<?php
include 'db.php';
include 'toast.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Toast Test</title>
    <!-- Include Toastr + jQuery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body>

<?php
toastError("Parent ID is required and must be a positive integer.");
?>

</body>
</html>