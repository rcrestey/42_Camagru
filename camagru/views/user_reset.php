<?php
session_start();
if (isset($_SESSION['logged_on_user']))
{
    $_SESSION['flash']['success'] = "You 're already connected !";
    header('Location:/views/gallery.php');
    die();
}
?>

<html>
<head> 
    <meta charset="utf-8">
    <link rel="stylesheet" href="/common/css/user_login.css">
    <link rel="stylesheet" href="/common/css/header.css">
    <script src="/common/js/main.js"></script>
</head>

<?php 
require_once 'print_flash.php';
require_once 'print_error.php';
require_once 'print_header.php'; 
?>

<body>
    <div id='wrapper'>
        <div class="main-content">
            <div class="header">
                <h1>Camagru</h1>
            </div>
            <div class="l-part">
                <form id='form_reset_user' onsubmit="return form_action('form_reset_user')" action="user.reset" method="POST">
                <input type="text" name="mail" placeholder="Mail" class="input">
                <input type="text" name="username" placeholder="Username" class="input"> 
                <input type="password" name="newpassword" placeholder="New Password" class="input">
                <input type="submit" name="sub" value="Reset" class="btn">
                </form>
            </div>
        </div>
        <div class="sub-content">
            <div class="s-part">
            Don't have an account ? <a href="/views/user_create.php">Sign up</a> <br/>
            Remember yout Password ?  <a href="/views/user_login.php">Sign in</a>
            </div>
        </div> 
    </div>
<script src="/common/js/main.js"></script>

<?php  require_once 'print_footer.php'; ?>