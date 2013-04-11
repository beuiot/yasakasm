<?php
$database_host="localhost";
$database_login="yasakasm";
$database_password="password";
$database="yasakasm";

function myTruncate($string, $limit, $break=" ", $pad="...") { // return with no change if string is shorter than $limit
	if(strlen($string) <= $limit) return $string; // is $break present between $limit and the end of the string?
	if(false !== ($breakpoint = strpos($string, $break, $limit))) {
		if($breakpoint < strlen($string) - 1) {
			$string = substr($string, 0, $breakpoint) . $pad;
		}
	}
	return $string;
}

function getRandomString($length = 6) {
    $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ";
    $validCharNumber = strlen($validCharacters);
 
    $result = "";
 
    for ($i = 0; $i < $length; $i++) {
        $index = mt_rand(0, $validCharNumber - 1);
        $result .= $validCharacters[$index];
    }
 
    return $result;
}

function generate_error(&$errors, $code, $message) {
	$error = new stdClass();
	$error->code = $code;
	$error->message = $message;
	array_push($errors, $error);
}
function generate_message(&$messages, $code, $message) {
	$d = new stdClass();
	$d->code = $code;
	$d->message = $message;
	array_push($messages, $d);
}

function get_parameter($name, $default) {
	if (isset($_GET[$name])) {
		return $_GET[$name];
	} else {
		return $default;
	}
}

function post_parameter($name, $default) {
	if (isset($_POST[$name])) {
		return $_POST[$name];
	} else {
		return $default;
	}
}


?>
