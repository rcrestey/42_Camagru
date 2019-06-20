<?php

require_once '../models/user.php';
require_once '../common/view.php';
require_once '../dao/factory.php';

use models\User;
use common\View;
use dao\Factory;

session_start();
//Check if get method
if(isset($_GET['action']))
{
	$action = $_GET['action'];
	UserController::$action($_GET);
}

////Check if post method
if(isset($_POST['action']))
{
	$action = $_POST['action'];
	UserController::$action($_POST);
}

class   UserController
{
    public static $error = array('error');

    public static function create($req)
    {
        $mail = htmlspecialchars($req['mail']);
        $username = htmlspecialchars($req['username']);
        $password = htmlspecialchars($req['password']);
        try {
            UserController::_mailCheck($mail);
            UserController::_usernameCheck($username);
            UserController::_passwordCheck($password);
            if (isset($mail, $username, $password) && empty(self::$error[1]))
            {
                $user = new User();
                $facto = new Factory();

                date_default_timezone_set("Europe/Paris"); 
                $user->set_mail($mail);
                $user->set_username($username);
                $user->set_password(password_hash($password, PASSWORD_BCRYPT));
                $user->set_notification(1);
                $user->set_keycheck(bin2hex(random_bytes('8')));
                $user->set_confirmed(0);
                $user->set_last_seen(date("Y-m-d H:i:s"));

                $id = $facto->create($user);
                
                //send mail
                $url = $_SERVER['HTTP_ORIGIN'] . "/controllers/user.php?action=createconfirm&id=$id&username=" . $user->get_username() . "&keycheck=" . $user->get_keycheck() . "";
                $headers = "From:" . $from;
                mail($user->get_mail(), 'Camagru - Confirm your account', "Welcom to Camagru ! </br> Validate You Account <a href=\"$url\">Just here !</a>", $headers);

                $_SESSION['flash']['success'] = "Well done! Check your mail for confirmed yout account !";
                View::show('user_login', null);
            }
            else
                View::show('user_create', self::$error);
        } catch (Exception $e) {
            if ($e->getCode() === "23000")
                array_push(self::$error, "Email or username Already use");
            else
                array_push(self::$error, $e->getMessage());
            View::show('user_create', self::$error);
        }
    }

    public static function createconfirm($req)
    {
        $user = new User();
        $facto = new Factory();
        $user->set_id($req['id']);
        $user = $facto->read($user);
        if (isset($user) && $user->get_username() === $req['username'] && $user->get_keycheck() === $req['keycheck'] && $user->get_confirmed() === '0')
        {
            $user->set_confirmed(1);
            $user->set_keycheck(1);
            $facto->update($user);
            $_SESSION['flash']['success'] = "account has been successfully confirmed <br/> You can login now";
        }
        else
            $_SESSION['flash']['error'] = "Error please check your confirmation link";
        View::show('user_login', null);
    }

    public static function reset($req)
    {
        $mail = htmlspecialchars($req['mail']);
        $username = htmlspecialchars($req['username']);
        $newpassword = htmlspecialchars($req['newpassword']);
        UserController::_mailCheck($mail);
        UserController::_usernameCheck($username);
        UserController::_passwordCheck($newpassword);
        if (isset($mail, $username) && empty(self::$error[1]))
        {
            $user = new User;
            $facto = new Factory();
        
            $user = $facto->id($user, array("username", $username));
            if (isset($user) && $user->get_username() === $username && $user->get_mail() === $mail )
            { 
                if ($user->get_confirmed() === '1' && $user->get_keycheck() === '1')
                {
                    $user->set_keycheck(bin2hex(random_bytes('8')));
                    $facto->update($user);

                    $url = $_SERVER['HTTP_ORIGIN'] . "/controllers/user.php?action=resetconfirm&id=" . $user->get_id() ."&username=" . $user->get_username() . "&keycheck=" . $user->get_keycheck() . "&password=" . password_hash($newpassword, PASSWORD_BCRYPT) . "";
                    $headers = "From:" . $from;
                    mail($user->get_mail(), 'Camagru - Reset Your password', "Welcom to Camagru ! </br>  Reset Your password <a href=\"$url\">Just here !</a>", $headers);
       

                    $_SESSION['flash']['success'] = "Well done! Check your mail change your password !";
                    View::show('user_login', null);
                    die();
                }
                else
                    $_SESSION['flash']['error'] = "Your account is not confirmed, check yout mailbox";
            }
            else
                $_SESSION['flash']['error'] = "Informations are incorects";
        }
        View::show('user_reset', self::$error);
    }

    public static function resetconfirm($req)
    {
        $user = new User();
        $facto = new Factory();
        $user->set_id($req['id']);
        $user = $facto->read($user);
        if (isset($user) && isset($req['password']) && $user->get_username() === $req['username'] && $user->get_keycheck() === $req['keycheck'] && $user->get_confirmed() === '1')
        {
            $user->set_password($req['password']);
            $user->set_keycheck(1);
            $facto->update($user);
            $_SESSION['flash']['success'] = "Your password has been successfully changed <br/> You can login now";
        }
        else
            $_SESSION['flash']['error'] = "Error please check your reset link";
        View::show('user_login', self::$error);
    }

    public static function updatePassword($req)
    {
        UserController::_loginCheck();
        UserController::_passwordCheck(htmlspecialchars($req['password']));
        $user = new User();
        $facto = new Factory();
        $user->set_id($_SESSION['logged_on_user']);
        $user = $facto->read($user);
        if (isset($user) && empty(self::$error[1]))
        {
            $user->set_password(password_hash(htmlspecialchars($req['password']), PASSWORD_BCRYPT));
            $facto->update($user);
            $_SESSION['flash']['success'] = "Password succesfully modified";
        }
        else
            $_SESSION['flash']['error'] = "Password modificarion Error";
        View::show('user_update', self::$error);
    }

    public static function updateUsername($req)
    {
        UserController::_loginCheck();
        UserController::_usernameCheck(htmlspecialchars($req['username']));
        $user = new User();
        $facto = new Factory();
        $user->set_id($_SESSION['logged_on_user']);
        $user = $facto->read($user);
        if (isset($user) && empty(self::$error[1]))
        {
            $user->set_username(htmlspecialchars($req['username']));
            $facto->update($user);
            $_SESSION['username'] = $req['username'];
            $_SESSION['flash']['success'] = "Username succesfully modified";
        }
        else
            $_SESSION['flash']['error'] = "Username modificarion Error";
        View::show('user_update', self::$error);
    }

    public static function updateMail($req)
    {
        UserController::_loginCheck();
        UserController::_mailCheck(htmlspecialchars($req['mail']));
        $user = new User();
        $facto = new Factory();
        $user->set_id($_SESSION['logged_on_user']);
        $user = $facto->read($user);
        if (isset($user) && empty(self::$error[1]))
        {
            $user->set_mail(htmlspecialchars($req['mail']));
            $facto->update($user);
            $_SESSION['mail'] = $req['mail'];
            $_SESSION['flash']['success'] = "Email succesfully modified";
        }
        else
            $_SESSION['flash']['error'] = "Email modificarion Error";
        View::show('user_update', self::$error);
    }

    public static function updateNotification($req)
    {
        UserController::_loginCheck();
        $facto = new Factory();
        $user = new User();

        $user->set_id($_SESSION['logged_on_user']);
        $user = $facto->read($user);
        if (isset($user) && empty(self::$error[1]))
        {
            $user->set_notification(($user->get_notification() == 1) ? '0' : '1');
            $facto->update($user);
            $_SESSION['notification'] = $user->get_notification();
        }
        else
            $_SESSION['flash']['error'] = "Email modificarion Error";
        View::show('user_update', self::$error);
    }

    public static function login($req)
    {
        $user = new User();
        $facto = new Factory();
       
        $user->set_username(htmlspecialchars($req['username']));
        $user = $facto->id($user, array("username", htmlspecialchars($req['username'])));
        if (isset($user) && password_verify(htmlspecialchars($req['password']), $user->get_password()) && $user->get_confirmed() == 1 && $user->get_keycheck() == 1)
        {
            $_SESSION['logged_on_user'] = $user->get_id();
            $_SESSION['username'] = $user->get_username();
            $_SESSION['mail'] = $user->get_mail();
            $_SESSION['notification'] = $user->get_notification();
            $_SESSION['flash']['success'] = "You are now connected";
            header('Location:/controllers/image.php?action=showpage&page=1');
            exit();
        }
        else
        {
            if (isset($user) && ($user->get_confirmed() != 1 || $user->get_keycheck() != 1))
                $_SESSION['flash']['error'] = "Your account is not confirmed, please check your mail"; 
            else
                $_SESSION['flash']['error'] = "Wrong login information, please retry";
            View::show('user_login', null);
        }
    }

    private static function _loginCheck()
    {
        if (!isset($_SESSION['logged_on_user']))
        {
            $_SESSION['flash']['error'] = "You need to login for see this page";
            View::show('user_login', null);
        }
        return (1);
    }
 
    private static function _mailCheck($mail)
    {
        if (filter_var($mail, FILTER_VALIDATE_EMAIL) === FALSE)
            array_push(self::$error, 'Invalid Email');
    }

    private static function _usernameCheck($username)
    {
        if (strlen($username) < '4') 
            array_push(self::$error, "Your username Must Contain At Least 4 Characters!");
        if (strlen($username) > '24') 
            array_push(self::$error, "Your username Must Contain 24 Characters maximum!");
        if (!preg_match("#^[a-zA-Z0-9]*$#", $username))
            array_push(self::$error, "Your username Must Contain only alpha char and number");
    }

    private static function _passwordCheck($password)
    {
        if (strlen($password) < '8')
             array_push(self::$error, "Your Password Must Contain At Least 8 Characters!");
        if (strlen($password) > '24')
             array_push(self::$error, "Your Password Must Contain 24 Characters maximum!");
        if (!preg_match("#[0-9]+#", $password))
             array_push(self::$error, "Your Password Must Contain At Least 1 Number!");
        if (!preg_match("#[A-Z]+#", $password))
             array_push(self::$error, "Your Password Must Contain At Least 1 Capital Letter!");
        if (!preg_match("#[a-z]+#", $password))
             array_push(self::$error, "Your Password Must Contain At Least 1 Lowercase Letter!");
    }
}