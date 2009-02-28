<?php
	include("common-admin.php");
	$textID=$HTTP_GET_VARS["text"];
	$textID=($textID>0)?$textID:0;
?>
<html>
<head>
<title><?php echo $SiteMainURL ?> - управление - редактирование текста</title>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
<?php include("../style.php")?>
<style>
body	{background-color:#AAAAAA}
</style>
</head>
<body marginwidth=0 marginheight=0 onload="document.forms['text_go_form'].submit()">
<table width=100% border=0 height=100% cellpadding=0 cellspacing=0>
<tr><td height=25 align=center><input type=button class=button value="закрыть окно редактирования" onclick="self.close()"></td></tr>
<tr><td height=*>
<iframe name="text_editor" border=0 width=100% height=100% marginwidth=0 marginheight=0 scrolling=no frameborder=0></iframe>
</td></tr>
<form method=post action=post.php enctype="multipart/form-data" name=text_go_form target=text_editor><input type=hidden name=textID value=<?php echo $textID?>></form>
</table>
</body>
</html>