<?php 
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
include 'secret/database.php';


$login = get_parameter('login', null);
$password = get_parameter('password', null);

$json_result = new stdClass();
$json_result->connected = 'NO';

if ($login != null && $login != '' && $password != null &&  $password != '') {
	// Connects to your Database 
	$link = mysql_connect($database_host, $database_login, $database_password) or die(mysql_error()); 
	mysql_select_db($database) or die(mysql_error());

	$query = "SELECT `user_id` from `users` WHERE `login` = '".$login."' AND `password` = '".$password."' LIMIT 1;";
	$result = mysql_query($query);
	if (mysql_num_rows($result) == 1) {
		$user = mysql_fetch_object($result);
		$id = $user->user_id;
		$sid = getRandomString(64);
		$query = "UPDATE `yasakasm`.`users` SET `sid` = '".$sid."' WHERE `users`.`user_id` =".$id." LIMIT 1;";

		mysql_query($query);
		$json_result->sid = $sid;
		$json_result->login = $login;
		$json_result->connected = 'YES';
	}
} else {
	if (isset($_SERVER['REMOTE_USER']) && $_SERVER['REMOTE_USER'] != null && $_SERVER['REMOTE_USER'] != '') {
		// Connects to your Database 
		$link = mysql_connect($database_host, $database_login, $database_password) or die(mysql_error()); 
		mysql_select_db($database) or die(mysql_error());
		$query = "SELECT `user_id` from `users` WHERE `login` = '".$_SERVER['REMOTE_USER']."' LIMIT 1;";
		$result = mysql_query($query);
		if (mysql_num_rows($result) == 1) {
			$user = mysql_fetch_object($result);
			$id = $user->user_id;
			$sid = getRandomString(64);
			$query = "UPDATE `yasakasm`.`users` SET `sid` = '".$sid."' WHERE `users`.`user_id` =".$id." LIMIT 1;";

			mysql_query($query);
			$json_result->sid = $sid;
			$json_result->login = $_SERVER['REMOTE_USER'];
			$json_result->connected = 'YES';
		}
	}
}


echo json_encode($json_result);


mysql_close($link);
?> 
