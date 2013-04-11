<?php 
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

include 'secret/database.php';


$errors = array();
$messages = array();

$action = get_parameter('action', 'list');
$parameters = get_parameter('parameters', null);
$id = get_parameter('id', null);
$login = get_parameter('login', null);
$login = json_decode($login);
$pagecount = 0;
$userid = 0;

//generate_message($messages, 2, "Action : ".$action);


if (!isset($action)) {
	generate_error($errors, 1, "No action set");
} else {

	if ($parameters == null) {
		$parameters = new stdClass();
		$parameters->page = 1;
		$parameters->pagelength = 10;
		$parameters->orderby = '';
		$parameters->orderby_way = 'ASC';
		$parameters->search = '';
		$parameters->search_field = 'keywords';
		$parameters->search_user_id = -1;
	} else {
		$parameters = json_decode($parameters);
	}
	
	// Connects to your Database 
	$link = mysql_connect($database_host, $database_login, $database_password) or die(mysql_error()); 
	mysql_select_db($database) or die(mysql_error()); 

	if ($login != null && isset($login->login) && isset($login->sid)) {
		$query = "SELECT `user_id` from `users` WHERE `login` = '".$login->login."' AND `sid` = '".$login->sid."' LIMIT 1;";
		$result = mysql_query($query);
		if (mysql_num_rows($result) == 1) {
			$row = mysql_fetch_object($result);
			$userid = $row->user_id;
		}
	}

	if ($action == 'create') {
		//generate_message($messages, 2, "Creating...");
		$snipplet = get_parameter('snipplet', null);
		if ($snipplet == null) {
			generate_error($errors, 11, "Erreur interne : pas de données");
			$action = "list";
		} else {
			$snipplet = json_decode($snipplet);
			$query = "INSERT INTO `".$database."`.`snipplets` (`snipplet_id`, `user`, `description`, `snipplet`, `keywords`, `date`) VALUES (NULL, ".$userid.", '".addslashes($snipplet->description)."', '".addslashes($snipplet->snipplet)."', '".addslashes($snipplet->keywords)."', NOW());";
			generate_message($messages, 2, $query);			
			mysql_query($query);

			generate_message($messages, 11, "Snipplet ajouté");
			$action = "details";
			$id = mysql_insert_id();
		}
	} else if ($action == 'edit') {
		$snipplet = get_parameter('snipplet', null);
		//echo $snipplet;
		generate_message($messages, 11, $snipplet);
		if ($snipplet == null) {
			generate_error($errors, 11, "Erreur interne : pas de données");
			$action = "list";
		} else {
			$snipplet = json_decode($snipplet);
			$query = "UPDATE `yasakasm`.`snipplets` SET `description` = '".addslashes($snipplet->description)."', `snipplet` = '".addslashes($snipplet->snipplet)."', `keywords` = '".addslashes($snipplet->keywords)."' WHERE `snipplets`.`snipplet_id` = ".addslashes($snipplet->snipplet_id).";";
			generate_message($messages, 2, $query);			
			mysql_query($query);

			generate_message($messages, 11, "Snipplet modifié");
			
			$action = "details";
			$id = $snipplet->snipplet_id;
		}
	}


//generate_message($messages, 2, "Action (before list) : ".$action);

	if ($action == 'list') {
		
		//generate_message($messages, 2, "Getting list...");

		$json_result = new stdClass();
	
		$where_used = false;


		
		$offset = $parameters->pagelength * ($parameters->page - 1);
		$snipplets = array();
		$query = "SELECT SQL_CALC_FOUND_ROWS `snipplet_id`,`description`, `snipplet`, `keywords`, `date`, `login` ";
		$query .= "from snipplets LEFT OUTER JOIN `users` ON `snipplets`.`user` = `users`.`user_id`";
		
		if ($parameters->search != '' && $parameters->search_field != '') {
			$query .= " WHERE `".$parameters->search_field."` LIKE '%{$parameters->search}%'";
			$where_used = true;
		}

		if ($parameters->search_user_id != -1) {
			if ($where_used == true) {
				$query .= " AND ";
			} else {
				$query .= " WHERE ";
			}

			$query .= "`user_id` = ".$parameters->search_user_id;
		}

		if ($parameters->orderby != '') {
			$query .= " ORDER BY `".$parameters->orderby."` ".$parameters->orderby_way;
		}
		$query .= " LIMIT ".$offset.",".$parameters->pagelength.";";
		generate_message($messages, 0, $query);
		$result = mysql_query($query);

		while ($row = mysql_fetch_object($result)) {
			$row->description = myTruncate($row->description, 40);
			$row->keywords = myTruncate($row->keywords, 40);
			$row->snipplet = myTruncate($row->snipplet, 100);
			array_push($snipplets, $row);
		}

		$json_result->snipplets = $snipplets;
		
		$result = mysql_query("SELECT FOUND_ROWS() as totalcount;");
		$row = mysql_fetch_object($result);
		$pagecount = CEIL($row->totalcount / $parameters->pagelength);
		

	} else if ($action == 'details') {
		//generate_message($messages, 21, "details");
		if ($id == null) {
			generate_error($errors, 21, "pas d'id");
		} else {
			$query = "SELECT * from snipplets LEFT OUTER JOIN `users` ON `snipplets`.`user` = `users`.`user_id` WHERE snipplet_id = ".$id.";";
			$result = mysql_query($query);
			$snipplet = mysql_fetch_object($result);
			//$snipplet->snipplet = nl2br($snipplet->snipplet);
			
			
			$files = array();
			$directory = 'uploads/'.$snipplet->snipplet_id.'/';
			if (is_dir($directory) && $handle = opendir($directory)) {

				/* This is the correct way to loop over the directory. */
				while (false !== ($file = readdir($handle))) {
					if ($file != ".." && $file != ".") {
						array_push($files, $file);
					}
				}

				closedir($handle);
			}
			$snipplet->files = $files;
			$json_result->snipplet = $snipplet;
		}
	}
}




//generate_error($errors, 11, "Erreur de test 1");
//generate_error($errors, 11, "Erreur de test 2");
//generate_message($messages, 11, "Message de test 1");
//generate_message($messages, 11, "Message de test 2");

$json_result->errors = $errors;
$json_result->messages = $messages;
$json_result->parameters = $parameters;
$json_result->pagecount = $pagecount;


echo json_encode($json_result);
mysql_close($link);


?> 
