<?php
session_start();
include("lib/db.class.php");
if (!is_file(__DIR__ . '/config.php')) {
    header('Location: install.php');
    exit;
}
require_once __DIR__ . '/config.php';
$db = new DB($config['database'], $config['host'], $config['username'], $config['password']);
$tbl_name = "stock_user"; // Table name

// username and password sent from form 
$myusername = $_POST['username'] ?? '';
$mypassword = $_POST['password'] ?? '';

if (!is_string($myusername) || !is_string($mypassword)) {
    http_response_code(400);
    exit;
}

$myusername = mysqli_real_escape_string($db->connection, $myusername);
$mypassword = mysqli_real_escape_string($db->connection, $mypassword);

$sql = "SELECT * FROM $tbl_name WHERE username='$myusername' and password='$mypassword'";
$result = mysqli_query($db->connection, $sql);
// mysqli_num_row is counting table row
$count = mysqli_num_rows($result);
// If result matched $myusername and $mypassword, table row must be 1 row

if ($count == 1) {
// Register $myusername, $mypassword and redirect to file "dashboard.php"
    $row = mysqli_fetch_row($result);
    session_regenerate_id(true);

    $_SESSION['id'] = $row[0];
    $_SESSION['username'] = $row[1];
    $_SESSION['usertype'] = $row[3];

    if ($row[3] == "admin")
        header("location: add_sales.php");
    else
        die("Not Valid User Type. Check with your application administartor");

} else {
    header("location: index.php?msg=Wrong%20Username%20or%20Password&type=error");
}
?>