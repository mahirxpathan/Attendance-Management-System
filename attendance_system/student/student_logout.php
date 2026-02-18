<?php
// Prevent browser from caching the dashboard
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

session_start();
$_SESSION = array();
session_destroy();

// Redirect to homepage
header("Location: ../index.php");
exit();
?>