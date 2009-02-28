<?php
	include("common-admin.php");
	$Result='';
	if($HTTP_POST_VARS['action']=='changepassword'){
		$sql="update `moderators` set `password`=md5('" . $HTTP_POST_VARS['password'] . "') where `id`=" . $SessionSettings['id'];
		if($db->sql_query($sql)){
			$Result='Пароль изменён!!!';
		}else{
			$sqlerror=$db->sql_error();
			die($sqlerror['message']);
		};
	};
	if($HTTP_POST_VARS['action']=='changesuppresshelp'){
		$SessionSettings['suppresshelp']=($SessionSettings['suppresshelp']==1)?0:1;
		$sql="update `moderators` set `suppresshelp`=" . $SessionSettings['suppresshelp'] . " where `id`=" . $SessionSettings['id'];
		if(!$db->sql_query($sql)){
			$sqlerror=$db->sql_error();
			die($sqlerror['message']);
		};
		WriteSessionSettings();
	};
	include ("top.php");
?>
<script>
function checkTheForm(theForm){
	if(String(theForm.password.value).length<5){
		alert('Слишком короткий пароль!');
		theForm.password.focus();
		return false;
	};
	if(theForm.password.value!=theForm.password2.value){
		alert('Не совпадают версии паролей!');
		theForm.password.focus();
		return false;
	};
	return true;
};
</script>
<center><span style="color:red;"><?php echo $Result?></span></center>
<?php echo drwTableBegin('100%',0)?>
<tr><td colspan=2 class=header align=left>логин "<?php echo $SessionSettings['login']?>", изменение пароля доступа</td></tr>
<%
	if(SessionSettings['suppresshelp']!=1)Response.Write('<tr><td class=data1 colspan=2><p class=main>При помощи этой формы Вы можете сменить свой пароль. Не забывайте о том, что из соображений безопасности не следует делать слишком просто пароль (например слово "пароль" или "password"). Во избежание ситуаций коротокого или отсутствующего пароля введено органичение на минимальное количество символов в пароле.</p></td></tr>');
%>
<form method=post onsubmit="return checkTheForm(this);"><input type=hidden name=action value="changepassword">
<tr><td align=right class=data2>Новый пароль:</td><td class=data2 align=left><input type=password class=text name=password size=40></td></tr>
<tr><td align=right class=data1>Подтверждение:</td><td class=data1 align=left><input type=password class=text name=password2 size=40></td></tr>
<tr><td class=data2 align=center colspan=2><input type=submit class=button value="изменить"></td></tr></form>
</form>
<?php
	echo drwTableEnd() . "<br>" . drwTableBegin('100%',0);
?>
<form method=post><input type=hidden name=action value=changesuppresshelp>
<tr><td class=header>Контекстная помощь</td></tr>
<?php
	if($SessionSettings['suppresshelp']!=1)echo "<tr><td class=data1><p class=main>Практически все разделы системы управления содержимым сайта имеют контекстную помощь об их назначении и способах работы с ними (например как этот текст). Вы можете включать/выключать отображение контекстной помощи при помощи этой формы.</p></td></tr>";
?>
<tr><td class=data2 align=center><input type=submit class=button value="<?php echo ($SessionSettings['suppresshelp']==1)?'ВКЛЮЧИТЬ контекстную помощь по управлению сайтом':'ВЫКЛЮЧИТЬ контекстную помощь по управлению сайтом'?>">
</td></tr>
</form>
<?php
	echo drwTableEnd();
	include ("bottom.php");
?>
