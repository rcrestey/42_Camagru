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
require_once 'print_error.php';  
require_once 'print_header.php'; ?>

<body>
    <div id="wrapper">
        <div class="main-content">
            <div class="header">
                <h1>Camagru</h1>
            </div>
            <div class="l-part">
                <form id='form_create_user' onsubmit="return form_action('form_create_user')" action="user.create" method="POST">
                    <input type="text" name="mail" placeholder="Mail" class="input">
                    <input type="text" name="username" placeholder="Username" class="input">
                    <input type="password" name="password" placeholder="Password" class="input">
                    <input type="submit" name="sub" value="Sign up" class="btn">
                </form>
            </div>
        </div>
            
        <div class="sub-content">
            <div class="s-part">
            Already have an account? <a href="/views/user_login.php">Sign in</a>
            </div>
        </div> 
    </div>

    <?php  require_once 'print_footer.php'; ?>