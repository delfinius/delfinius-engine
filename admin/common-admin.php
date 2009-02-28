<?php
    $site_root = "../";
    define("SITE_ROOT",true);
    include('../define/common.php');
    
    if(!($SessionSettings["id"]>0)){
	header("Location: login.php");
	exit();
    };
?>
