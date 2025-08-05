<?php
// Toast notification functions with safe JS escaping
function toastNotify($type, $message) {
    $safeMessage = json_encode($message);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function () {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: '3000'
            };
            toastr['$type']($safeMessage);
        });
    </script>";
}

function toastSuccess($message) {
    toastNotify('success', $message);
}

function toastError($message) {
    toastNotify('error', $message);
}
?>
