<?php
session_start();
session_unset();
session_destroy();
echo "<script>alert('Anda telah logout.');
location='login.php';</script>";
?>