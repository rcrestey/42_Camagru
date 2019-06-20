<?php
require_once '../models/like.php';
require_once '../models/user.php';
require_once '../models/image.php';
require_once '../common/view.php';
require_once '../dao/factory.php';

use models\Like;
use models\User;
use models\Image;
use common\View;
use dao\Factory;

define('LIKE_NB', '10');

session_start();
//Check if get method
if (isset($_GET['action']))
{
	$action = $_GET['action'];
	LikeController::$action($_GET);
}

////Check if post method
if (isset($_POST['action']))
{
	$action = $_POST['action'];
	LikeController::$action($_POST);
}

class   LikeController
{
    public static function showlikes($req)
    {
        if (isset($req['image_id']) && !empty($req['image_id']) && is_numeric($req['image_id']) && isset($req['path']) && !empty($req['path']) )
        {
            $facto = new Factory();
            $like = new Like();
            $data = array();
            $tmp = array();

            $total = $facto->total($like, array('image_id', $req['image_id']));
            $pagenb = ($total % LIKE_NB != 0) ? intdiv($total, LIKE_NB) + 1 : intdiv($total, LIKE_NB);
            if (isset($req['page']) && is_numeric($req['page']) && $req['page'] >= 1 && $req['page'] <= $pagenb)
            {
                $likes = $facto->ids($like, array("image_id", $req['image_id']), array(($req['page'] - 1) * LIKE_NB, LIKE_NB));
                foreach ($likes as $lik)
                {
                    $user = new User();
                    $user->set_id($lik->get_liker_id());
                    $user = $facto->read($user);
                    $tmp['liker'] = $user->get_username();
                    $tmp['date'] = $lik->get_creation_date();
                    array_push($data, $tmp);
                }
                $data = base64_encode(serialize($data));
                View::show('image_like', array('image_id' => $req['image_id'], 'path' => $req['path'], 'page' => $req['page'], 'pagenb' =>$pagenb, 'likes' => urlencode($data)));
            }
            else
            {
                View::show('image_like', array('image_id' => $req['image_id'], 'path' => $req['path'], 'page' => $req['page'], 'pagenb' =>$pagenb, 'likes' => null));
                exit();
            }
        }
        else
        {
            $_SESSION['flash']['error'] = "This page does not exist";
            $url = $_SERVER['HTTP_ORIGIN'] . "/controllers/image.php?action=showpage&page=1";
            header('Location:'.$url);
        }
    }

    public static function create($req)
    {
        if ((isset($req['image_id'], $req['page']) && !empty($req['page']) && !empty($req['image_id'])) && isset($_SESSION['logged_on_user']))
        {
            if (LikeController::isliked($req['image_id']))
            {
                $_SESSION['flash']['error'] = "You have already like this image";
                $url = "/controllers/image.php?action=showpage&page=" . $req['page'];
                header('Location:'.$url);
                exit();
            }
            $facto = new Factory();
            $like = new Like();
            $img = new Image();

            date_default_timezone_set("Europe/Paris"); 
            $img->set_id($req['image_id']);
            $img = $facto->read($img);
            $img->set_likes($img->get_likes() + 1);
            $facto->update($img);

            $like->set_image_id($req['image_id']);
            $like->set_liker_id($_SESSION['logged_on_user']);
            $like->set_creation_date(date("Y-m-d H:i:s"));
            $facto->create($like);


            $_SESSION['flash']['success'] = "Image liked !";
            $url = "/controllers/image.php?action=showpage&page=" . $req['page'];
            header('Location:'.$url);
            
        }
        else
        {
            if (!isset($_SESSION['logged_on_user']))
                $_SESSION['flash']['error'] = "You are not connected";
            else
                $_SESSION['flash']['error'] = "Error: invalid field";
            $url = "/controllers/image.php?action=showpage&page=". $req['page'];
            header('Location:'.$url);
            exit();
        }
    }

    public static function delete($req)
    {
        if ((isset($req['image_id'], $req['page']) && !empty($req['page']) && !empty($req['image_id'])) && isset($_SESSION['logged_on_user']))
        {
            if (LikeController::isliked($req['image_id']))
            {
                $like = new Like;
                $facto = new Factory();
                $img = new Image();

                $img->set_id($req['image_id']);
                $img = $facto->read($img);
                $img->set_likes($img->get_likes() - 1);
                $facto->update($img);
                $like = $facto->id($like, array('image_id', $req['image_id'], 'liker_id', $_SESSION['logged_on_user']));
                $facto->delete($like);

                $_SESSION['flash']['success'] = "Image unliked !";
                $url = "/controllers/image.php?action=showpage&page=" . $req['page'];
                header('Location:'.$url);
                exit();
            }
        }
        $_SESSION['flash']['error'] = "Error: invalid field";
        $url = "/controllers/comment.php?action=showcomments&image_id=".$req['image_id']."&path=".$req['path'];
        header('Location:'.$url);
        
    }

    public static function isliked($image_id)
    {
        $like = new Like;
        $facto = new Factory();

        if (isset($image_id) && isset($_SESSION['logged_on_user']))
        {
            $like = $facto->id($like, array('image_id', $image_id, 'liker_id', $_SESSION['logged_on_user']));
            if ($like == null)
                return (0);
            else
                return (1);
        }
    }
}
