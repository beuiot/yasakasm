<?php 
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
include 'secret/database.php';

$json_result = new stdClass();
$users = array();
// Connects to your Database 
$link = mysql_connect($database_host, $database_login, $database_password) or die(mysql_error()); 
mysql_select_db($database) or die(mysql_error());

$query = "SELECT `login`, `user_id` from `users`;";
$result = mysql_query($query);
while ($row = mysql_fetch_object($result)) {
	array_push($users, $row);
}

$json_result->users = $users;

echo json_encode($json_result);

mysql_close($link);


?> 
