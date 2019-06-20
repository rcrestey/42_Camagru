<?php
require_once '../models/image.php';
require_once '../controllers/like.php';

use models\Image;

$page = $_GET['page'];
$pagenb = $_GET['pagenb'];
$data = unserialize(base64_decode(urldecode($_GET['data'])));
?>


<html>
<head> 
    <meta charset="utf-8">
    <link rel="stylesheet" href="/common/css/gallery.css">
    <link rel="stylesheet" href="/common/css/header.css">
    <script src="/common/js/main.js"></script>
</head>


<?php 
require_once 'print_flash.php';
require_once 'print_error.php';
require_once 'print_header.php'; 
?>

<body>


<div> 
    <div class="container">
	    <div class="gallery">
    <?php $i = 1;  if (isset($data) && !empty($data) && isset($pagenb) && !empty($pagenb) && isset($page) && !empty($page)): foreach ($data as $image):?>

<div class="gallery-item" tabindex="0">


            <figure style=" border-style: solid; border-color: black;">
                <img src=<?=$image->get_path()?> class="gallery-image"/> 
                <figcaption>
                    <?php if (LikeController::isliked($image->get_id())) :?>
                    <form class="like_form" id='form_delete_like<?=$image->get_id()?>' onsubmit="return form_action('form_delete_like<?=$image->get_id()?>')" action="like.delete" method="POST">
                        <input type="hidden" name="image_id" value=<?=$image->get_id()?> />
                        <input type="hidden" name="page" value=<?=$page?> />
                        <input class="likeon" type="image" name="unlike" src="/common/icon/likeon.svg" width="42vw"/>
                    </form>
                    <?php elseif (isset($_SESSION['logged_on_user'])): ?>
                    <form class="like_form" id='form_create_like<?=$image->get_id()?>' onsubmit="return form_action('form_create_like<?=$image->get_id()?>')" action="like.create" method="POST">
                        <input type="hidden" name="image_id" value=<?=$image->get_id()?> />
                        <input type="hidden" name="page" value=<?=$page?> />
                        <input class="likeoff" type="image" name="like" src="/common/icon/likeoff.svg" width="42vw"/>
                    </form>
                    <?php endif;?>
                    <a class="img_like" href="/controllers/like.php?action=showlikes&page=1&image_id=<?=$image->get_id()?>&path=<?=$image->get_path()?>"> Likes(<?= $image->get_likes()?>)</a>
                    <a class="img_comment" href="/controllers/comment.php?action=showcomments&page=1&image_id=<?=$image->get_id()?>&path=<?=$image->get_path()?>" > Comments(<?= $image->get_comments()?>)</a>
                </figcaption>
            </figure>

</div>

    <?php if ($i % 3 == 0) echo "</tr><tr>"; $i++;?>
    <?php endforeach;?>

    </div>
</div>
</div>
    <div class="pagenb">
    <?php $i = 0; while(++$i <= $pagenb):?>
        <a class="<?php echo(($page == $i) ? "here" : "nothere"); ?>" href="/controllers/image.php?action=showpage&page=<?=$i?>"><?=$i?></a>
    <?php endwhile; else:?>
        <h1>No images avaliable !</h1>
    <?php endif;?>
    </div>
<script src="/common/js/main.js"></script>

<?php  require_once 'print_footer.php'; ?>
