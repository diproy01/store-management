<?php
session_start();
if (!isset($_SESSION['db_name'], $_SESSION['host'], $_SESSION['user'], $_SESSION['pass'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/db.class.php';
if ($config !== [
    'database' => $_SESSION['db_name'],
    'host' => $_SESSION['host'],
    'username' => $_SESSION['user'],
    'password' => $_SESSION['pass'],
]) {
    header('Location: index.php');
    exit;
}
$db = new DB($config['database'], $config['host'], $config['username'], $config['password']);
if ($db->countOfAll('stock_user') > 0) {
    header('Location: index.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['uname'] ?? '';
    $password = $_POST['password'] ?? '';
    $answer = $_POST['answer'] ?? '';
    if (!is_string($username) || !is_string($password) || !is_string($answer)
        || trim($username) === '' || $password === '' || trim($answer) === '') {
        http_response_code(400);
        exit('Enter a username, password, and recovery answer.');
    }
    $stmt = $db->connection->prepare("INSERT INTO stock_user (id, username, password, user_type, answer) VALUES (1, ?, ?, 'admin', ?)");
    try {
        $stmt->execute([trim($username), $password, $answer]);
    } catch (mysqli_sql_exception $exception) {
        if ($exception->getCode() !== 1062) {
            throw $exception;
        }
        header('Location: index.php');
        exit;
    }
    session_regenerate_id(true);
    unset($_SESSION['db_name'], $_SESSION['host'], $_SESSION['user'], $_SESSION['pass']);
    $_SESSION['id'] = 1;
    $_SESSION['username'] = trim($username);
    $_SESSION['usertype'] = 'admin';
    $_SESSION['initial_store_setup'] = true;
    header('Location: update_details.php');
    exit;
}
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Store Management - User</title>

    <!-- Stylesheets -->

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cmxform.css">
    <link rel="stylesheet" href="js/lib/validationEngine.jquery.css">

    <!-- Scripts -->
    <script src="js/lib/jquery.min.js" type="text/javascript"></script>
    <script src="js/lib/jquery.validate.min.js" type="text/javascript"></script>

    <script>
        /*$.validator.setDefaults({
         submitHandler: function() { alert("submitted!"); }
         });*/

        $(document).ready(function () {

            // validate signup form on keyup and submit
            $("#login-form").validate({
                rules: {
                    uname: {
                        required: true,
                        minlength: 5
                    },
                    password: {
                        required: true,
                        minlength: 5
                    },
                    answer: {
                        required: true,
                        minlength: 5
                    }
                },
                messages: {
                    uname: {
                        required: "Please Enter The User Name",
                        minlength: "Your User Name must consist of at least 5 characters"
                    },
                    password: {
                        required: "Please Enter The Password",
                        minlength: "Your Password must be at least 5 characters long"
                    },
                    answer: {
                        required: "Please Enter Security Question Answer",
                        minlength: "Your Security Question Answer must be at least 5 characters long"
                    }
                }
            });

        });

    </script>

    <!-- Optimize for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>

<!--    Only Index Page for Analytics   -->

<!-- TOP BAR -->
<div id="top-bar">

    <div class="page-full-width">

        <!--<a href="#" class="round button dark ic-left-arrow image-left ">See shortcuts</a>-->

    </div>
    <!-- end full-width -->

</div>
<!-- end top-bar -->


<!-- HEADER -->
<div id="header">

    <div class="page-full-width cf">

        <div id="login-intro" class="fl">

            <h1>User Details </h1>


        </div>
        <!-- login-intro -->

        <?php require_once __DIR__ . '/tpl/store_brand.php'; renderStoreBrand('company-branding'); ?>

    </div>
    <!-- end full-width -->

</div>
<!-- end header -->



<!-- MAIN CONTENT -->
<div id="content">

    <form action="" method="POST" id="login-form" class="cmxform" autocomplete="off">

        <fieldset>

            <p>
                <label>UserName</label>
                <input type="text" name="uname" id="uname" class="round full-width-input" placeholder="Enter User Name"
                       autofocus/>
            </p>

            <p>
                <label>Password</label>
                <input type="password" name="password" id="password" class="round full-width-input"
                       placeholder="Enter Password" autofocus/>
            </p>

            <p>
                <label>Security Question</label>
                What's your favorite movie?
                <input type="text" name="answer" id="answer" class="round full-width-input" placeholder="Enter Answer"
                       autofocus/>
            </p>

            <!--<a href="dashboard.php" class="button round blue image-right ic-right-arrow">LOG IN</a>-->
            <input type="submit" class="button round blue image-right ic-right-arrow" name="submit" value="Next"/>
            &nbsp;</fieldset>
    </form>

</div>
<!-- end content -->


</body>
</html>

