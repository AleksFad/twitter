<?php
include('libs/php/config.php');

if (isset($_POST['login_username']) && isset($_POST['login_password'])){
	$username = $_POST['login_username'];
	$password = $_POST['login_password'];
	
	$query = ('SELECT * FROM '.$prefix.'_users WHERE username = "'.$username.'" and password = "'.md5($password).'" LIMIT 1');
	$result = $mysqli->query($query);
	
	if ($result){
		
		$data = $result->fetch_assoc();
		
		$query = ('UPDATE '.$prefix.'_users SET session = "'.session_id().'" WHERE id = '.$data['id']);
		$result = $mysqli->query($query);
		//Get and set the current session id
		$_SESSION['vorgurakendused_4'] = session_id();
	}
	
}

header('Location: index.php');

?>