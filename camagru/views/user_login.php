<?php 
session_start();
 if (isset($_SESSION['logged_on_user']))
 {
     $_SESSION['flash']['success'] = "You 're already connected !";
     header('Location:/controllers/image.php?action=showpage&page=1');
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


<?php  require_once 'print_header.php'; 
require_once 'print_flash.php';?>

<body>
    <div id='wrapper'>
        <div class="main-content">
            <div class="header">
                <h1>Camagru</h1>
            </div>
            <div class="l-part">
                        <form id='form_login_user' onsubmit="return form_action('form_login_user')" action="user.login" method="POST">
                        <input type="text" name="username" placeholder="Username" class="input"/>
                        <div class="overlap-text">
                            <input type="password" name="password" placeholder="Password" class="input"/> 
                            <a href="/views/user_reset.php">Forgot?</a>
                        </div>
                        <input type="submit" name="sub" value="log in" class="btn">
                        </form>
            </div>
        </div>

        <div class="sub-content">
            <div class="s-part">
            Don't have an account? <a href="/views/user_create.php">Sign up</a>
            </div>
        </div> 
    </div>
    <?php  require_once 'print_footer.php'; ?>