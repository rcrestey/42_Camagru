<?php
if (session_status() == PHP_SESSION_NONE)
    session_start(); 
?>
<header>
<ul class="header" role="navigation">
    <?php if (isset($_SESSION['logged_on_user'])) : ?>
        <li><a href="/views/montage.php"><img src="/common/icon/montage.svg" class="icon"/></a></li>
        <li><a href="/controllers/image.php?action=showpage&page=1"><img src="/common/icon/gallery.svg" class="icon"/></a></li>

        <li class="title"> Camagru </li>

        <li><a href="/views/user_update.php"><img src="/common/icon/user.svg" class="icon"/></a></li>
        <li><a href="/views/user_logout.php"><img src="/common/icon/logout.svg" class="icon"/></a></li>
    <?php else :?>
        <li><a href="/controllers/image.php?action=showpage&page=1"><img src="/common/icon/gallery.svg" class="icon"/></a></li>
        
        <li class="title"> Camagru </li>
        <li><a href="/views/user_login.php" class="button">Sign in</a></li>
        <li><a href="/views/user_create.php" class="button">Sign up</a></li>
    <?php endif;?>
</ul>
</header>