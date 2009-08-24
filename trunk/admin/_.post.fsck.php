<?php
include('common-admin.php');
include_once("fckeditor/fckeditor.php");

$theTextID = 1;
$theTextID=($HTTP_POST_VARS['textID']>0)?$HTTP_POST_VARS['textID']:$theTextID;
$textValue='';

if($HTTP_POST_VARS['action']=='update'){
	$sql="update texts set text='" . str_replace("'","''", $HTTP_POST_VARS['txteditor']) . "' where id=" . $theTextID;
	if(!$db->sql_query($sql)){
		$sqlerror=$db->sql_error();
		die($sqlerror['message']);
	};
};


$sql="select `text` from `texts` where `id`=" .$theTextID;
$db->sql_query($sql);
if($db->sql_numrows()>0){
	$textValue=$db->sql_fetchfield('text');
};
?>

<html>
<head>
<title>Визуальный HTML-редактор</title>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
<?php include ('../style.php')?>
</head>
<form method="post" style="margin:0"><input type="hidden" name="action" value="update"><input type="hidden" name="textID" value="<?php echo $theTextID?>">
<?php
$oFCKeditor = new FCKeditor('txteditor') ;
$oFCKeditor->BasePath = '/admin/fckeditor/' ;
$oFCKeditor->Config["CustomConfigurationsPath"] = "/admin/fckeditor/_config.js";
$oFCKeditor->ToolbarSet = 'delfinius-engine';
$oFCKeditor->Height = '100%';
$oFCKeditor->Value = $textValue ;
$oFCKeditor->Create() ;
echo '';
?>
</form>
</body></html>