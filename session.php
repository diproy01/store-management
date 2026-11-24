<?php
session_start(); // Use session variable on this page. This function must put on the top of page.
if (!isset($_SESSION['username']) || ($_SESSION['usertype'] ?? null) !== 'admin') { // if session variable "username" does not exist.
    header("location: index.php?msg=Please%20login%20to%20access%20admin%20area%20!");
    exit;
}
error_reporting(0);
include("lib/db.class.php");
if (!is_file(__DIR__ . '/config.php')) {
    header('Location: install.php');
    exit;
}
require_once __DIR__ . '/config.php';
error_reporting(0);

?>