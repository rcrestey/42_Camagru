<?php 
$start_view = 'image_showpage&page=1';
$view = (isset($_GET['view'])) ? $_GET['view'] : "";
if(!empty($view)){
	$controller = explode("_", $view)[0];
	$action = explode("_", $view)[1];
    header('Location:/controllers/'.$controller.'.php?action='.$action);
} else {
	$controller = explode("_", $start_view)[0];
	$action = explode("_", $start_view)[1];
    header('Location:/controllers/'.$controller.'.php?action='.$action);
}
?>