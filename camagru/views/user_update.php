<?php
session_start();
if (!isset($_SESSION['logged_on_user']))
{
        $_SESSION['flash']['error'] = "You need to connect for see this page";
        header('Location:/views/user_login.php');
        die();
}

?>



<html>
<head> 
    <meta charset="utf-8">
    <link rel="stylesheet" href="/common/css/header.css">
    <link rel="stylesheet" href="/common/css/user_update.css">
    <script src="/common/js/main.js"></script>
</head>


<?php 
require_once 'print_flash.php';
require_once 'print_error.php';
require_once 'print_header.php'; ?>

<body>

<div id="wrapper">
<div class="main-content">
            <div class="header">
                <h1>Preferences</h1>
        </div>
        <div class="l-part">
                <form id='form_update_mail' onsubmit="return form_action('form_update_mail')" action="user.updateMail" method="POST">
                        <input type="text" name="mail" placeholder=<?= $_SESSION['mail']?> class="input">
                        <input type="submit" name="sub" value="CHANGE" class="btn">
                </form>

                <form id='form_update_username' onsubmit="return form_action('form_update_username')" action="user.updateUsername" method="POST">
                        <input type="text" name="username" placeholder=<?= $_SESSION['username']?> class="input" >
                        <input type="submit" name="sub" value="CHANGE" class="btn" >
                </form>
                <form id='form_update_password' onsubmit="return form_action('form_update_password')" action="user.updatePassword" method="POST">
                        <input type="password" name="password" placeholder="Password" class="input">
                        <input type="submit" name="sub" value="CHANGE"class="btn">
                </form>

                <div class="wrap_notif">
                        <p>Notification: </p>
                        <form id='form_update_notification' onsubmit="return form_action('form_update_notification')" action="user.updateNotification" method="POST">
                                <input type="image" name="sub" src="/common/icon/<?php if ($_SESSION['notification'] == 1) echo "on"; else echo "off" ?>.svg" class="notif">
                        </form>
                </div>
                </div>
        </div>
</div>

<script src="/common/js/main.js"></script>

<?php  require_once 'print_footer.php'; ?>
