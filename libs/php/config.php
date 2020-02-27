<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');

session_start();

$limit = 5000000; // File size limit
$path = 'http://dijkstra.cs.ttu.ee/~alfade/prax4/'; // URL path
$prefix = '155047_prax4'; // MySQL table prefix

// MySQLi
$mysqli = new mysqli(
	'host', 
	'pass',
	'user',
	'base'
);
if ($mysqli->connect_error) echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
$mysqli->query("SET NAMES 'utf8'");

//XSS
foreach ($_POST as $key => $value) {
	//If first string is not massive
	if (!is_array($value)) {
		// real escape string escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection
		$_POST[$key] = $mysqli->real_escape_string($value);
	} else {
		foreach ($value as $k => $v) {
			if (!is_array($v)) {
				$_POST[$key][$k] = $mysqli->real_escape_string($v);
			}
		}
	}
}
foreach ($_GET as $key => $value) {
	if (!is_array($value)) {
		$_GET[$key] = $mysqli->real_escape_string($value);
	} else {
		foreach ($value as $k => $v) {
			if (!is_array($v)) {
				$_GET[$key][$k] = $mysqli->real_escape_string($v);
			}
		}
	}
}

// Functions
function get_user($user = 0) {
	global $mysqli, $prefix;
	//Get the integer value of a variable
	$user = intval($user);
	
	if ($user > 0) {
		$query = ('SELECT * FROM '.$prefix.'_users WHERE id = '.$user);
		//Performs a query on the database
		$result = $mysqli->query($query);
		if ($result && $result->num_rows > 0) {
			//Fetch a result row as array
			$data = $result->fetch_assoc();
			return $data;
		}
	} elseif (isset($_SESSION['vorgurakendused_4'])) {
		$query = ('SELECT * FROM '.$prefix.'_users WHERE session = "'.$_SESSION['vorgurakendused_4'].'"');
		$result = $mysqli->query($query);
		if ($result && $result->num_rows > 0) {
			$data = $result->fetch_assoc();
			return $data;
		}
	}
	return false;
}

function check_follow($following) {
	global $mysqli, $prefix;

	$user = get_user();
	$following = intval($following);
	
	$query = ('SELECT * FROM '.$prefix.'_followers WHERE user_id = "'.$user['id'].'" AND following_id = "'.$following.'"');
	/*var_dump($query);*/
	$result = $mysqli->query($query);
	if ($result && $result->num_rows > 0) {
		return true;
	}
	return false;
}