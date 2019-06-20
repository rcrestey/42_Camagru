<?php
require_once '../models/comment.php';
require_once 'print_header.php';

use models\Comment;

$path = $_GET['path'];
$image_id = $_GET['image_id'];
$page = $_GET['page'];
$pagenb = $_GET['pagenb'];
$comments = unserialize(base64_decode(urldecode($_GET['comments'])));?>
<?php if (isset($path, $comments, $image_id)): ?>

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

<h1> Comments: </h1>
<div>
    <img src=<?=$path?> />
</div>
<div>
<table>
    <?php if (isset($_SESSION['logged_on_user'])) : ?>
        <tr>
            <form id='form_create_comment' onsubmit="return form_action('form_create_comment')" action="comment.create" method="POST">
                <input type="hidden" name="image_id" value="<?= $image_id?>"/>
                <input type="hidden" name="path" value="<?=$path?>" />
                <td><?= $_SESSION['username']?></td>
                <td> <input type="text" name="content"/></td>
                <td> <input type="submit" name="sub" value="comment"/></td>
            </form>
        </tr>
    <?php endif;?>
    <?php $i=0; if (isset($page, $pagenb) && !empty($pagenb) && !empty($page)): foreach ($comments as $comment): $i++ ?>
        <tr>
            <td><?= $comment['owner']?></td>
            <td><?= $comment['content']?></td>
            <td><?= $comment['date']?></td>
        </tr>
    <?php endforeach; endif;?>
    <?php if ($i == 0):?>
        <p>No comment for this image</p>
        <?php   endif; ?>

</table>
</div>
<div>
<?php $i = 0; while(++$i <= $pagenb):?>
    <a id=<?php echo(($page == $i) ? "here" : "nothere"); ?> href="/controllers/comment.php?action=showcomments&page=<?=$i?>&image_id=<?=$image_id?>&path=<?=$path?>"><?=$i?></a>
<?php endwhile;?>
</div>
<?php
else:  
        header('Location: /controllers/image.php?action=showpage&page=1'); 
endif;?>
<script src="/common/js/main.js"></script>

<?php  require_once 'print_footer.php'; ?>