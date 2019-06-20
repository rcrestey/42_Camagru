<?php
 namespace dao;

 require_once 'database.php';

 use dao\Database;
 use \PDO;

define('ACTION_ID', 'id');
define('ACTION_IDS', 'ids');
define('ACTION_SEARCH', 'search');
define('ACTION_CREATE', 'create');
define('ACTION_READ', 'read');
define('ACTION_UPDATE', 'update');
define('ACTION_DELETE', 'delete');
define('ACTION_TOTAL', 'total');
define('IMG_NB', '9');
define('COMMENT_NB', '5');

class Factory 
{
private $_database;
private $_object;

public function __construct()
{
    $this->_database = new Database();
    $this->_database->load();  
}

public function total($object, $criteria)
{
	$this->_object = $object;
	$query = $this->_generate_query(ACTION_TOTAL, $criteria, null);
  return $this->_execute_query($query, ACTION_TOTAL);
}

public function search($object, $criteria, $rank)
{ 
  $this->_object = $object;
	$query = $this->_generate_query(ACTION_SEARCH, $criteria, $rank);
  return $this->_execute_query($query, ACTION_SEARCH);
}

public function id($object, $criteria)
{ 
  $this->_object = $object;
	$query = $this->_generate_query(ACTION_ID, $criteria, null);
	return $this->_execute_query($query, ACTION_ID);
}

public function ids($object, $criteria, $rank)
{ 
  $this->_object = $object;
	$query = $this->_generate_query(ACTION_IDS, $criteria, $rank);
	return $this->_execute_query($query, ACTION_IDS);
}

public function create($object)
{
  $this->_object = $object;
  $query = $this->_generate_query(ACTION_CREATE, null, null);
  return $this->_execute_query($query, ACTION_CREATE);
}

public function read($object)
{
  $this->_object = $object;
  $query = $this->_generate_query(ACTION_READ, null, null);
  return $this->_execute_query($query, ACTION_READ);
}

public function update($object)
{
  $this->_object = $object;
  $query = $this->_generate_query(ACTION_UPDATE, null, null);
	$this->_execute_query($query, ACTION_UPDATE);
}

public function delete($object)
{
  $this->_object = $object;
  $query = $this->_generate_query(ACTION_DELETE, null, null);
  $this->_execute_query($query, ACTION_DELETE);
}

private function _generate_query($action, $criteria, $rank)
{
		/*
			get object class name
			del namespace,
			name in small capitalize
		*/
		$entity = strtolower(end(explode('\\',get_class($this->_object)))) . 's';

		$attributes = ""; // list des colomne
	
		// list object proprieteries
		$properties = $this->_object->properties();
	
		// lister le nom des propriétés
		$properties_names = $this->_object->properties_names();

		// récupérer le nombre d'attributs
		$nb_attribute = count($properties_names); 

		foreach ($properties_names as $key => $value) 
		{
			if($key != 'id')
				// créer la chaine de colonnes à partir des attributs
				$attributes .= ($key < $nb_attribute - 1) ? $value . ", " : $value;
		}
		
		$values = array();
		$format = "";
		$settings = "";
		$where = "";
		$object_id = "";

		foreach ($properties as $key => $value) 
		{
			// récupérer l'id
			if($key == 'id') $object_id = $value;

			if($key != 'id')
			{
				//format prepare query
				$format .= ($key != end($properties_names)) 
				? "?, " : "?";

				// lister les valeurs
				array_push($values, $value);
				
			// lister les assignations
			$settings .= ($key != end($properties_names))
			? $key."=?, " : $key."=?";

				// lister les éléments de la clause WHERE
				$where .= ($key != end($properties_names)) 
				? $key." LIKE '%".$criteria."%' OR " : $key." LIKE '%".$criteria."%' ";
			}
		}
		// construire la requête en fonction de l'action
		switch ($action) 
		{
			case ACTION_ID:
				return array("SELECT * FROM $entity WHERE $criteria[0] = ?". (isset($criteria[2]) ? " AND $criteria[2] = ?" : ""), ( isset($criteria[3]) ? array($criteria[1], $criteria[3]) :  array($criteria[1])));
				break;
			case ACTION_IDS:
				return array("SELECT * FROM $entity WHERE $criteria[0] = ? ORDER BY creation_date DESC"  . (($rank[0] !== null) ? " LIMIT ".$rank[0].", ".$rank[1] : "" ), array($criteria[1]));
				break;
			case ACTION_CREATE:
				return 	array("INSERT INTO $entity ($attributes) VALUES ($format) ", $values);
				break;
			
			case ACTION_READ:
				return	array("SELECT * FROM $entity WHERE id = ? ", array($object_id));
				break; 			

			case ACTION_UPDATE:
				array_push($values, $object_id);
				return 	array("UPDATE $entity SET $settings WHERE id = ?", $values);
				break; 	

			case ACTION_DELETE:
				return array("DELETE FROM $entity WHERE id = ? ", array($object_id)); 
				break;
			case ACTION_SEARCH:
				return array("SELECT * FROM $entity ORDER BY $criteria DESC" . (($rank !== null) ? " LIMIT $rank, " . IMG_NB : ""), array());
				break;
			case ACTION_TOTAL:
				return array("SELECT count(*) AS nb FROM $entity" . (($criteria !== null) ? " where $criteria[0]= ?" : ""), array((($criteria !== null) ? $criteria[1] : "")));
			default:
				return "";
				break;
		}
	}

	private function _execute_query($query, $action)
	{
		$result = null;
		$pdo = $this->_database->get_connection();
		try {
		$req = $pdo->prepare($query[0]);
		$req->execute($query[1]);
		}  catch (PDOException $e) {
			throw new PDOException($e->getMessage(), (int)$e->getCode());
	} 
		if ($action == ACTION_CREATE)
			$result = $pdo->lastInsertId();
		if ($action == ACTION_READ || $action == ACTION_ID)
			$result = $this->_get_result($req, ACTION_READ);
		if ($action == ACTION_SEARCH || $action == ACTION_IDS) 
			$result = $this->_get_result($req, ACTION_SEARCH);
		if ($action == ACTION_TOTAL)
			$result = $req->fetchAll()['0']['nb'];
		return $result;
	}


private function _get_result($req, $action)
{ 
		// lister le nom des propriétés
		$properties_names = $this->_object->properties_names();

		// récupérer le nombre d'attributs
		$nb_attribute = count($properties_names); 

		$list = array();
	    while($row = $req->fetch(PDO::FETCH_ASSOC)) 
	    {	
			$object = null;
	    	// récupérer la valeur des champs
				$values = array(); 
	    	$index = 0;
	    	foreach ($row as $key => $value) 
	    	{
	    		array_push($values, $value);

	    		if($index ++ == $nb_attribute-1) 
	    		{
	    			$index = 0;
	    			$object = $this->_set_object($values);
	    		}
	    	}
	    	array_push($list, $object);
	    }
		if($action == ACTION_READ)
			// retourner l'objet
			return $object;
		else 
			return $list;
	}

	private function _set_object($values)
	{
		// identifier le nom de la classe de l'entity courante
		$class = get_class($this->_object);
		
		// intancier un nouvel objet
		$new = new $class(); 
			
		// lister les propriétés de l'objet
		$properties = $new->properties();

		// initialiser les propriétés de l'objet avec les valeurs
		$index = 0;
		foreach ($properties as $key => $value) {
			$method = "set_".$key;
			$new->$method($values[$index ++]);
		}
		return $new;
	}
}