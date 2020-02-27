<?php

include('libs/php/config.php');

if (isset($_SESSION['vorgurakendused_4'])) {
	$query = ('UPDATE '.$prefix.'_users SET session = "" WHERE session = "'.$_SESSION['vorgurakendused_4'].'"');
	$result = $mysqli->query($query);
}

session_destroy();
//session_regenerate_id() will replace the current session id with a new one, and keep the current session information.
session_regenerate_id();

header('Location: index.php');
?>