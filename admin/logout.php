<?php
	if(!session_id())session_start();
	$HTTP_SESSION_VARS=array();
	header("Location: login.php");
	exit();
?>
