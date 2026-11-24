<?php
session_start();
if (is_file(__DIR__ . '/config.php')) {
    header('Location: index.php');
    exit;
}
if (isset($_POST['host'], $_POST['username'], $_POST['password'])
    && is_string($_POST['host']) && is_string($_POST['username']) && is_string($_POST['password'])) {
    $_SESSION['host'] = trim($_POST['host']);
    $_SESSION['user'] = trim($_POST['username']);
    $_SESSION['pass'] = $_POST['password'];
}
if (empty($_SESSION['host']) || empty($_SESSION['user'])) {
    header('Location: install.php');
    exit;
}
try {
    $link = new mysqli($_SESSION['host'], $_SESSION['user'], $_SESSION['pass']);
    $databases = $link->query('SHOW DATABASES');
} catch (mysqli_sql_exception $exception) {
    header('Location: install.php?msg=' . rawurlencode('Database connection failed. Check your settings.'));
    exit;
}
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Store Management - Login to Control Panel</title>

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
            document.getElementById('create').checked = true;
            document.getElementById('select_box').disabled = true;

            // validate signup form on keyup and submit
            $("#login-form").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    }

                },
                messages: {
                    name: {
                        required: "Please Enter The Database Name",
                        minlength: "Your Database must consist of at least 3 characters"
                    }
                }
            });

        });
        function create_data() {
            document.getElementById("select_box").disabled = true;
            document.getElementById("name").disabled = false;

        }
        function select_data() {
            document.getElementById("select_box").disabled = false;
            document.getElementById("name").disabled = true;

        }
    </script>
    <style type="text/css">

    </style>
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

            <h1>DataBase </h1>


        </div>
        <!-- login-intro -->

        <?php require_once __DIR__ . '/tpl/store_brand.php'; renderStoreBrand('company-branding'); ?>

    </div>
    <!-- end full-width -->

</div>
<!-- end header -->


<!-- MAIN CONTENT -->
<div id="content">
    <form action="setup_page.php" method="POST" id="login-form" class="cmxform" autocomplete="off">

        <fieldset>
            <p> <?php

                if (isset($_REQUEST['msg'])) {

                    $msg = $_REQUEST['msg'];
                    echo "<p style=color:red>$msg</p>";
                }
                ?>

            </p>

            <p>
                <input type="radio" value="1" name="select[]" id="create"
                       onclick="create_data()">Create New Database
                <input type="text" id="name" class="round full-width-input" name="name" autofocus/>
            </p>

            <p>
                <input type="radio" name="select[]" id="select" onclick="select_data()">Select Created DataBase<br>
                <select name="select_box" class="round full-width-input" id="select_box"
                        style="padding: 5px 10px 5px 10px; border: 1px solid #D9DBDD;">
                    <?php while ($database = $databases->fetch_row()) {
                        if (in_array($database[0], ['information_schema', 'mysql', 'performance_schema', 'sys'], true)) {
                            continue;
                        }
                        $name = htmlspecialchars($database[0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        echo '<option value="' . $name . '">' . $name . '</option>';
                    } ?>
                </select>

            </p>
            <input type="submit" class="button round blue image-right ic-right-arrow" name="submit" value="INSTALL"/>
        </fieldset>

    </form>

</div>
<!-- end content -->


</body>
</html>