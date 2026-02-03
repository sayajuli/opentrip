<?php
session_start();
session_destroy();
// Redirect ke Login dengan pesan logout
header("Location: login.php?pesan=logout");
exit;
?>