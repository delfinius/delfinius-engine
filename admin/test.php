<?php
    $site_root = "../";
    define("SITE_ROOT",true);
    include ("../define/common.php");
?>
<html>
<head>
<title><?php echo $SiteMainURL ?> - ����������</title>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
<?php include ("../style.php") ?>
<style>
body	{background-color:#000000}
</style>
</head>
<body marginwidth=0 marginheight=0 onload="document.forms['login'].UserName.focus();">
<?php
    echo $HTTP_POST_VARS['UserName'];
?>
<table width=100% height=100% border=0 cellpadding=0 cellspacing=0>
<tr><td align=center valign=middle height=100% >
<?php echo drwTableBegin(700,0) ?>
<form method=post name=login>
<tr><td colspan=2 class=header align=center>���� �� �������� ����������</td></tr>
<tr><td class=data1 align=right>�����:</td><td class=data1 align=left><input type=text class=text name=UserName size=40></td></tr>
<tr><td class=data1 align=right>������:</td><td class=data1 align=left><input type=password class=text name=Password size=40></td></tr>
<tr><td colspan=2 class=data2 align=center><input type=submit value="����" class=button></td></tr>
</form>
<?php echo drwTableEnd() ?>
<tr><td align=center valign=bottom height=50>
<a href="http://delfin.name" target=_blank>Delfin &copy; 2004</a>
</td></tr>
</td></tr></table>
</body>