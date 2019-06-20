<?php
require_once '../models/image.php';
require_once '../models/comment.php';
require_once '../models/like.php';
require_once '../models/calque.php';
require_once '../common/view.php';
require_once '../dao/factory.php';

use models\Image;
use models\Comment;
use models\Like;
use models\Calque;
use common\View;
use dao\Factory;

define('IMG_NB', '9');

session_start();

//Check if get method
if (isset($_GET['action']))
{
	$action = $_GET['action'];
	ImageController::$action($_GET);
}

////Check if post method
if (isset($_POST['action']))
{
	$action = $_POST['action'];
    ImageController::$action($_POST);
}

class   ImageController
{
    public static function showpage($req)
    {
        $facto = new Factory();
        $images = new Image();

        $total = $facto->total($images, null);
        if ($total != 0)
        {
            $pagenb = ($total % IMG_NB != 0) ? intdiv($total, IMG_NB) + 1 : intdiv($total, IMG_NB);
            if (isset($req['page']) && is_numeric($req['page']) && $req['page'] >= 1 && $req['page'] <= $pagenb)
            {
            $images = base64_encode(serialize($facto->search($images, 'creation_date', ($req['page'] - 1) * IMG_NB)));
            View::show('image_gallery', array('page' => $req['page'], 'pagenb' => $pagenb, 'data' => urlencode($images)));
            }
            else
            {
                $_SESSION['flash']['error'] = "This page does not exist";
                $url = $_SERVER['HTTP_ORIGIN'] . "/controllers/image.php?action=showpage&page=1";
                header('Location:'.$url);
            }
        }
        else
        {
            header('Location:/views/image_gallery.php');
        }
    }

    public static function showimage($req)
    {
        //header("Content-Type: text/plain");
        $facto = new Factory();
        $images = new Image();
        $tmp = array();
        $data = array();

        $total = $facto->total($images, null);
        if (isset($req['imgnb']))
            $imgnb = $req['imgnb'];
        else
            $imgnb = IMG_NB;
        $pagenb = ($total % $imgnb != 0) ? intdiv($total, $imgnb) + 1 : intdiv($total, $imgnb);
        if (isset($req['page']) && is_numeric($req['page']) && $req['page'] >= 1 && $req['page'] <= $pagenb)
        {
           $images = $facto->ids($images, array('user_id', $_SESSION['logged_on_user']), array(($req['page'] - 1) * $imgnb, $imgnb));
            foreach($images as $image)
            {
                $tmp['id'] = $image->get_id();
                $tmp['path'] = $image->get_path();
                $tmp['likes'] = $image->get_likes();
                array_push($data, $tmp);
            }
            if (isset($data) && !empty($data))
                echo json_encode($data);
            else
                echo "null";
        }
        else
            echo "null";

    }

    public static function del($req)
    {

        $id = $req['id'];
        if (isset($_SESSION['logged_on_user']) && isset($id))
        {
            $img = new Image();
            $like = new Like();
            $comment = new Comment();
            $facto = new Factory();

            $img->set_id($id);
            $img = $facto->read($img);
            if (!empty($img))
            {
                //check if image is owned by user
                if ($img->get_user_id() != $_SESSION['logged_on_user'])
                    exit('you can deleted only your images');
                //delete all comment link to this image
                $comments = $facto->ids($comment, array("image_id", $id), null);
                if (isset($comments) && !empty($comments))
                    foreach($comments as $comment)
                        $facto->delete($comment);
        
                //delete all like link to this images
                $likes = $facto->ids($like, array("image_id", $id), null);
                if (isset($likes) && !empty($likes))
                    foreach($likes as $like)
                        $facto->delete($like);

                //delete img
                unlink($img->get_path());
                $facto->delete($img);
                echo "Image successfully deleted";
            }
        }
        else
            echo "Error has occured, image no deleted";
    }

   public static function create($req)
   {
        date_default_timezone_set("Europe/Paris"); 
        $facto = new Factory();
        $calque = new Calque();
        $img = new Image();

        if (isset($_SESSION['logged_on_user']) && isset($req['data']) && isset($req['id']))
        {

            $calque->set_id($req['id']);
            $calque = $facto->read($calque);

            if (isset($calque) && !empty($calque))
            {
                //fonction data to img you return path
                $path = self::base64ToImage(urldecode($req['data']));
                self::superposition($path, $calque->get_path());
                //save into db
                $img->set_user_id($_SESSION['logged_on_user']);
                $img->set_path($path);
                $img->set_likes(0);
                $img->set_comments(0);
                $img->set_creation_date(date("Y-m-d H:i:s"));

                $facto->create($img);

                echo "ok";
                die();
            }
            
        }
        echo "null";
    }

    public static function upload($req)
    {
        date_default_timezone_set("Europe/Paris"); 
        if ($_FILES['image']['type'] === "image/png")
        {
            if ($_FILES['image']['size'] <= 150000)
            {
                if (isset($_SESSION['logged_on_user']))
                {
                    $facto = new Factory();
                    $img = new Image();
                    $id = uniqid();
                    $path =  "../common/img/".  $id . ".png";

                    
                    move_uploaded_file($_FILES['image']['tmp_name'], $path);
                    $img->set_user_id($_SESSION['logged_on_user']);
                    $img->set_path($path);
                    $img->set_likes(0);
                    $img->set_comments(0);
                    $img->set_creation_date(date("Y-m-d H:i:s"));

                    $facto->create($img);

                    echo "Well done! Image uploaded !";
                    die();
                }
            }
            else
                echo "Error: File too larges (150KB max)";
        }
        else
            echo "Error: need to be png image";
    }

    private static function base64ToImage($data) 
    {
        $id = uniqid();
        $file =  "../common/img/".  $id . ".png";
        
        $data = str_replace('data:image/png;base64,', '', $data);
        $data = str_replace(' ', '+', $data);
        $data = base64_decode($data);

        file_put_contents($file, $data); 
        return $file;
    }

    private static function superposition($file, $calque)
    {
        //merge calque
        // $dest = imagecreatefrompng($file);
        // $src = imagecreatefrompng(".." . $calque);
        // $new = imagecreatetruecolor(100,100);
        // $black = imagecolorallocate($new, 0, 0, 0);
        // imagecolortransparent($new, $black);
        // imagealphablending($new, true);
        // imagesavealpha($new, true);
        // imagecopyresampled($new, $src, 0, 0, 0, 0, 100,100,100,100);
        // imagedestroy($src);
        // $src = $new;
        
        // unlink($file);
        // imagealphablending($src, true);
        // imagesavealpha($src, true);
        // imagecopymerge($dest, $src, 112, 50, 0, 0, 110, 110, 100);
        // imagepng($dest, $file);

        // imagedestroy($dest);
        // imagedestroy($src);  


        $dest = imagecreatefrompng($file);

        $src = imagecreatefrompng(".." . $calque);
        $new = imagecreatetruecolor(100, 100);
        imagealphablending($new, false);
        imagesavealpha($new, true);

        $new_transparent = imagecolorallocatealpha($new, 255, 255, 255, 127);
        imagefilledrectangle($new, 0, 0, 100, 100, $new_transparent);
        imagecopyresampled($new, $src, 0, 0, 0, 0, 100, 100, 100, 100);

        imagedestroy($src);
        $src = $new;
        imagecopy($dest, $src, 112, 50, 0, 0, 100, 100);
        unlink($file);
        imagepng($dest, $file);
        imagedestroy($dest);
        imagedestroy($src);  

    }
}
