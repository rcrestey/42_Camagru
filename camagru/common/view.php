<?php namespace common;

// redirect to the good views

class View
{
	public static function show($view, $data)
	{
		$index = 0;
		$params = "";
		if (isset($data))
		{
			$nb_param = count($data);
			foreach ($data as $key => $value) {
				$index ++;
				$params .= "$key=$value&";
			}
		}	
		$params .= "view=$view";
		
		$url = "/views/$view.php?$params";
		header('Location:'.$url);
		exit();
	}
}