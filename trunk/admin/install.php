<?php
	$site_root = "../";
	define("SITE_ROOT",true);
	define("INSTALL_PROCESS",true);
	include("../define/common.php");
	if($coreParams["installed"]->Value)header("Location: /");
	include("../define/installer.php");
	if($HTTP_POST_VARS["action"]=="install"){
		$reinstall=($HTTP_POST_VARS["mode"]=="reinstall");
		InstallAll($reinstall);
		header("Location: /admin/");
	};
?>
<html>
<head>
<title><?php echo $SiteMainURL ?> - управление</title>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
<?php include ("../style.php") ?>
</head>
<body marginwidth=0 marginheight=0>
<table width=100% height=100% border=0 cellpadding=0 cellspacing=0>
<tr><td align=center valign=middle height=100% >
<?php echo drwTableBegin(700,0) ?>
<form method=post name=install><input type=hidden name=action value=install>
<tr><td colspan=2 class=header align=center>Создание структуры БД</td></tr>
<tr><td colspan=2 class=data1 align=left><p class=main>Структура базы данных сайта не создана или нарушена её целостность. Выберите режим создания структуры:</p></td></tr>
<tr><td class=data1 align=center><input type=radio name=mode value=reinstall></td><td class=data1 align=left> - <strong>Полное пересоздание.</strong> В этом случае будут уничтожены все данные, которые содержатся в базе даннх.</td></tr>
<tr><td class=data1 align=center><input type=radio name=mode value=repair checked></td><td class=data1 align=left> - <strong>Создание недостающих таблиц.</strong> В этом случае будут созданы таблицы, отсутствующие в базе данных. Данные хранящиеся в существующих таблицах останутся целыми.</td></tr>
</td></tr>
<tr><td colspan=2 class=data2 align=center><input type=submit value="продолжить" class=button></td></tr>
</form>
<?php echo drwTableEnd() ?>
<tr><td align=center valign=bottom height=50>
<a href="http://delfin.name" target=_blank>Delfin &copy; 2009</a>
</td></tr>
</td></tr></table>
</body>