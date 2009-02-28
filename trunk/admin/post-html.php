<?php
    include('common-admin.php');
	$theTextID=1;
	$Result='';
	$theTextID=($HTTP_POST_VARS['textID']>0)?$HTTP_POST_VARS['textID']:$theTextID;
	$InputHTML=$HTTP_POST_VARS['htmlsrc'];
	if(strlen($InputHTML)>0){
		$sql="update texts set text='" . str_replace("\'","''", $InputHTML) . "' where id=" . $theTextID;
		if(!$db->sql_query($sql)){
		    $sqlerror=$db->sql_error();
		    die($sqlerror['message']);
		};
	};
?>
<html>
<head>
<title>HTML-редактор</title>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
<style>
img.toolbtn	{filter:gray();border:solid 1px #808080}
</style>
</head>
<?php include('../style.php') ?>
<script>

var theTextID=<?php echo $theTextID ?>;

function tool_btn_over(theBtn){
	theBtn.style.border='solid 1px #000000';
	theBtn.style.filter='';
};

function tool_btn_out(theBtn){
	theBtn.style.border='solid 1px #808080';
	theBtn.style.filter='gray()';
};

function tool_btn_down(theBtn){
	theBtn.style.filter='Invert()';
};

function tool_btn_up(theBtn){
	theBtn.style.filter='';
};

function editinvisual(){
	if(confirm('Если вы редактировали текст, перед переходом в режим визуального редактирования необходимо сохранить изменения.\r\nПродолжить?'))
		document.forms['goinvisual'].submit();
};
</script>
<body>
<table border=0 width=100% height=100% cellpadding=0 cellspacing=0><tr><td bgcolor=#777777 c_lass=border><table cellpadding=0 cellspacing=2 width=100% height=100%>
<tr><td valign=top>
	<table width=100% border=0 cellpadding=0 cellspacing=0><tr><td class=border>
	<table width=100% border=0 cellpadding=2 cellspacing=1>
	<tr><td bgcolor=#AAAAAA colspan=4><img src=/toolbarimages/new.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_NEW' onclick="document.forms['savehtml'].htmlsrc.value='<p>&amp;nbsp;</p>'" alt="Очистить"><img
	 src=/toolbarimages/open.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_OPEN' onclick="document.forms['reloadsrc'].submit();" alt="Загрузить исходный вариант"><img
	 src=/toolbarimages/save.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_SAVE' onclick="document.forms['savehtml'].submit();" alt="Сохранить изменения"><img src=/format.gif border=0 width=10><img
	 src=/toolbarimages/html.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_HTML' onclick="editinvisual();" alt="Перейти к визуальному редактору"></td>
	</tr>
	</table></td></tr></table>
</td></tr>
<form name=savehtml method=post action=post-html.php>
<tr><td width=100% height=100% class=data2>
<textarea name=htmlsrc style="width:100%;height:100%;"><?php
	$sql='select text from texts where id=' . $theTextID;
	if($db->sql_query($sql)){
		echo CutQuots($db->sql_fetchfield('text'));
	}else{
	    $sqlerror=$db->sql_error();
	    die($sqlerror['message']);
	};
?></textarea>
</tr></td>
<input type=hidden name=textID value="<?php echo $theTextID ?>">
</form>
<form name=reloadsrc method=post>
<input type=hidden name=textID value="<?php echo $theTextID ?>">
</form>
<form name=goinvisual method=post action="post.php" enctype="multipart/form-data">
<input type=hidden name=textID value="<?php echo $theTextID ?>">
</form>
</table></td></tr></table>
</html>
