<?php

require_once '../models/calque.php';
require_once '../common/view.php';
require_once '../dao/factory.php';

use models\Calque;
use common\View;
use dao\Factory;

session_start();
//Check if get method
if (isset($_GET['action']))
{
	$action = $_GET['action'];
	CalqueController::$action($_GET);
}

////Check if post method
if (isset($_POST['action']))
{
	$action = $_POST['action'];
	CalqueController::$action($_POST);
}

class   CalqueController
{
    public static function showcalque($req)
    {
        $facto = new Factory();
        $calques = new Calque();
        $tmp = array();
        $data = array();

        $calques = $facto->search($calques, 'id', null);
        foreach($calques as $calque)
        {
            $tmp['id'] = $calque->get_id();
            $tmp['path'] = $calque->get_path();
            array_push($data, $tmp);
        }
        if (isset($data) && !empty($data))
            echo json_encode($data);
        else
            echo "null";
    }
}