<?php
require_once '../models/like.php';
use models\Like;

require_once 'print_header.php';

$path = $_GET['path'];
$image_id = $_GET['image_id'];
$page = $_GET['page'];
$pagenb = $_GET['pagenb'];
$likes = unserialize(base64_decode(urldecode($_GET['likes'])));?>
<?php if (isset($path, $likes, $image_id)): ?>

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


<h1> likes: </h1>
<div>
    <img src=<?=$path?> />
</div>
<div>
<table>
    <?php $i=0; if (isset($page, $pagenb) && !empty($pagenb) && !empty($page)): foreach ($likes as $like): $i++ ?>
        <tr>
            <td><?= $like['liker']?></td>
            <td><?= $like['date']?></td>
        </tr>
    <?php endforeach; endif;?>
    <?php if ($i == 0):?>
        <p>No likes for this image</p>
        <?php   endif; ?>

</table>
</div>
<div>
<?php $i = 0; while(++$i <= $pagenb):?>
    <a id=<?php echo(($page == $i) ? "here" : "nothere"); ?> href="/controllers/like.php?action=showlikes&page=<?=$i?>&image_id=<?=$image_id?>&path=<?=$path?>"><?=$i?></a>
<?php endwhile;?>
</div>
<?php
else:  
        header('Location: /controllers/image.php?action=showpage&page=1'); 
endif;?>
<script src="/common/js/main.js"></script>

<?php  require_once 'print_footer.php'; ?>