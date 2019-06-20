<?php
session_start();
unset($_SESSION['logged_on_user']);
$_SESSION['flash']['success'] = "You're now disconnected";
header('Location:/controllers/image.php?action=showpage&page=1');
?>