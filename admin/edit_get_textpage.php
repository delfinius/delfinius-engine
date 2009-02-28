<?php
    include ('common-admin.php');
    $theText = "<p>&nbsp;</p>";
    if( !empty($HTTP_GET_VARS['text'])){
	$sql='select text from texts where id=' . $HTTP_GET_VARS['text'];
	if($db->sql_query($sql)){
	    $theText=$db->sql_fetchfield('text');
	}else{
	    $sqlerror=$db->sql_error();
	    die($sqlerror['message']);
	};
    };
?>
<html>
<head>
<title>Текст для редактирования</title>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
<?php include('../style.php');?>
<style>
__body	{background-color:#FFFFFF;}
</style>
</head>
<body>
<?php echo $theText;?>
</body>
</html>