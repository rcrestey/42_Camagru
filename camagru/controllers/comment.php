<?php
require_once '../models/comment.php';
require_once '../models/image.php';
require_once '../models/user.php';
require_once '../common/view.php';
require_once '../dao/factory.php';

use models\Comment;
use models\Image;
use models\User;
use common\View;
use dao\Factory;

define('COMMENT_NB', '5');

session_start();
//Check if get method
if (isset($_GET['action']))
{
	$action = $_GET['action'];
	CommentController::$action($_GET);
}

////Check if post method
if (isset($_POST['action']))
{
	$action = $_POST['action'];
	CommentController::$action($_POST);
}

class   CommentController
{
    public static function showcomments($req)
    {
        if (isset($req['image_id']) && !empty($req['image_id']) && is_numeric($req['image_id']) && isset($req['path']) && !empty($req['path']) )
        {

            $facto = new Factory();
            $comment = new Comment();
            $data = array();
            $tmp = array();

            $total = $facto->total($comment, array('image_id', $req['image_id']));
            $pagenb = ($total % COMMENT_NB != 0) ? intdiv($total, COMMENT_NB) + 1 : intdiv($total, COMMENT_NB);
            if (isset($req['page']) && is_numeric($req['page']) && $req['page'] >= 1 && $req['page'] <= $pagenb)
            {
                $comments = $facto->ids($comment, array("image_id", $req['image_id']), array(($req['page'] - 1) * COMMENT_NB, COMMENT_NB));
                foreach ($comments as $com)
                {
                    $user = new User();
                    $user->set_id($com->get_owner_id());
                    $user = $facto->read($user);
                    $tmp['owner'] = $user->get_username();
                    $tmp['content'] = $com->get_content();
                    $tmp['date'] = $com->get_creation_date();
                    array_push($data, $tmp);
                }
                $data = base64_encode(serialize($data));
                View::show('image_comment', array('image_id' => $req['image_id'], 'path' => $req['path'], 'page' => $req['page'], 'pagenb' =>$pagenb, 'comments' => urlencode($data)));
            }
            else
            {
                View::show('image_comment', array('image_id' => $req['image_id'], 'path' => $req['path'], 'page' => $req['page'], 'pagenb' =>$pagenb, 'comments' => null));
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
        if ((isset($req['image_id'], $req['content'], $req['path']) && !empty($req['image_id']) && !empty($req['content'])) && isset($_SESSION['logged_on_user']))
        {
            if (strlen($req['content']) > 150)
            {
                $_SESSION['flash']['error'] = "Max size of comment is 150 char ";
                $url = "/controllers/comment.php?action=showcomments&page=1&image_id=" . $req['image_id'] . "&path=". $req['path'];
                header('Location:'.$url);
                exit();
            }
            else if (strlen($req['content']) < 2)
            {
                $_SESSION['flash']['error'] = " Min size of comment is 2 char";
                $url = "/controllers/comment.php?action=showcomments&page=1&image_id=" . $req['image_id'] . "&path=". $req['path'];
                header('Location:'.$url);
                exit();
            }
            $facto = new Factory();
            $comment = new Comment();
            $img = new Image();
            $user = new User();

            date_default_timezone_set("Europe/Paris"); 
            $img->set_id($req['image_id']);
            $img = $facto->read($img);
            $img->set_comments($img->get_comments() + 1);
            $facto->update($img);

            $user->set_id($img->get_user_id());
            $user = $facto->read($user);
            
            $comment->set_image_id($req['image_id']);
            $comment->set_owner_id($_SESSION['logged_on_user']);
            $comment->set_content(htmlspecialchars($req['content']));
            $comment->set_creation_date(date("Y-m-d H:i:s"));
            $facto->create($comment);
            if ($user->get_notification() == 1)
            {
                $headers = "From:" . $from;
                mail($user->get_mail(), 'Camagru - notification', "Your image has been commented by " . $_SESSION['username'] . ". Check it out on camagru !", $headers);           
            }


            $_SESSION['flash']['success'] = "Image commented !";
            $url = "/controllers/comment.php?action=showcomments&page=1&image_id=" . $req['image_id'] . "&path=". $req['path'];

            header('Location:'.$url);
            
        }
        else
        {
            $_SESSION['flash']['error'] = "Error: invalid field";
            $url = "/controllers/comment.php?action=showcomments&image_id=".$req['image_id']."&path=".$req['path'];
            header('Location:'.$url);
            exit();
        }
    }
}