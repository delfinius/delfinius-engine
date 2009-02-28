<?php
    include("common-admin.php");
	if(!HaveAccess('root'))header('Location: .');
	$Action='append';
	$ID=0;
	$Result='';
	function frmToPerms(){
	    global $permTypes, $HTTP_POST_VARS;
	    $UserPermitions=0;
	    foreach($permTypes as $aKey => $perms)
			if($aKey!='developer'){
				if($HTTP_POST_VARS['perms' . $aKey]=='on')$UserPermitions+=$perms[0];
			};
		return $UserPermitions;
	};
	if($HTTP_POST_VARS['action']=='update'){
		$sql='select `permitions` from `moderators` where `id`=' . $HTTP_POST_VARS['id'];
		$db->sql_query($sql);
		$CurrentAccess=$db->sql_fetchfield('permitions');
		$UserPermitions=frmToPerms();
		if(UserHaveAccess('developer',$CurrentAccess))$UserPermitions+=$permTypes['developer'][0];
		if($HTTP_POST_VARS['deleteuser']!='on'){
			$db->sql_query("select `id` from `moderators` where `login`='" . $HTTP_POST_VARS['login'] . "' and `id`<>" . $HTTP_POST_VARS['id']);
			if($db->sql_numrows()!=0){
			    $Result='Другой модератор с таким логином уже существует. Информация НЕ обновлена.';
			}else{
				$needpass=($HTTP_POST_VARS["changepassword"]!='on')?"'" . $HTTP_POST_VARS['passwordhash'] . "'":"'" . md5($HTTP_POST_VARS['password']) . "'";
			
				$sql="update `moderators` set `login`='" . $HTTP_POST_VARS['login'] . "', `Password`=" . $needpass . " ,  `EMail`='" . $HTTP_POST_VARS['email'] . "', `permitions`=" . $UserPermitions . ", `description`='" . $HTTP_POST_VARS['description'] . "', `name`='" . $HTTP_POST_VARS['name'] . "' where `id`=" . $HTTP_POST_VARS['id'];
				$db->sql_query($sql);
				$Result='Информация о модераторе обновлена';
			};
		}else{
			if(!UserHaveAccess('developer',$CurrentAccess)){
				$sql="delete from `moderators` where `id`=" . $HTTP_POST_VARS['id'];
				$db->sql_query($sql);
				$Result='Модератор удалён';
			}else{
				$Result='Модератор НЕ удалён. Это разработчик.';
			};
		};
	};

	if($HTTP_POST_VARS['action']=='append'){
		$sql="select `id` from `moderators` where `login`='" . $HTTP_POST_VARS['login'] . "'";
		$db->sql_query($sql);
		if($db->sql_numrows!=0){
			$Result='Модератор с таким логином уже существует. Создание нового НЕ осуществлено.';
		}else{
			$UserPermitions=frmToPerms();
			$sql="insert into `moderators` (`login`, `password`, `email`, `permitions`, `description`, `name`) values ('" . $HTTP_POST_VARS['login'] . "', '" . md5($HTTP_POST_VARS['password']) . "','" . $HTTP_POST_VARS['email'] . "',$UserPermitions,'" . $HTTP_POST_VARS['description'] . "','" . $HTTP_POST_VARS['name'] . "')";
			$db->sql_query($sql);
			$Result='Создан новый модератор';
		};
	};
	$Login='';
	$EMail='';
	$Password='';
	$Name='';
	$UserPermitions=0;
	$Description='';
	if($HTTP_POST_VARS['action']=='edit'){
		$sql="select `id`, `login`, `password`, `email`, `permitions`, `name`, `description` from `moderators` where `id`=" . $HTTP_POST_VARS['id'];
		if(!$db->sql_query($sql)){
		    $sqlerror=$db->sql_error();
		    die($sqlerror['message']);
		};
		if($row=$db->sql_fetchrow()){
			$ID=$row['id'];
			$Login=$row['login'];
			$Password=$row['password'];
			$EMail=$row['email'];
			$Name=$row['name'];
			$Description=$row['description'];
			$UserPermitions=$row['permitions'];
			$Action='update';
		};
	};

    include ("top.php");
?>
<script>
function checkTheForm(theForm){
	if(theForm.deleteuser)
		if(theForm.deleteuser.checked)return confirm('Вы точно хотите удалить пользователя?');

	if(String(theForm.login.value).length<3){
		alert('Слишком короткий логин!');
		theForm.login.focus();
		return false;
	};

	if(theForm.changepassword.checked)
		if(String(theForm.password.value).length<5){
			alert('Слишком короткий пароль!');
			theForm.password.focus();
			return false;
		};
	return true;
};
</script>
<center><span style="color:red;"><?php echo $Result?></span></center>
<?php
    echo drwTableBegin('100%',0);
    if($SessionSettings['suppresshelp']!=1)echo '<tr><td class=data1 colspan=2><p class=main>При помощи этого раздела Вы можете добавлять/изменять/удалять информацию о модераторах. Для добавления модератора воспользуйтесь приведённой ниже формой. Для изменяния информации о существующем модераторе - найдите его в списке существующих модераторов и нажмите кнопку "менять", после этого информация о модераторе появится в форме используемой для добавления.</p></td></tr>';
?>
<tr><td colspan=2 class=header align=center><?php echo ($Action=='update')?'изменение информации о модераторе':'добавить нового модератора'?></td></tr>
<form method=post onsubmit="return checkTheForm(this);"><input type=hidden name=action value="<?php echo $Action?>"><input type=hidden name=id value=<?php echo $ID?>>
<tr><td align=right class=data2>Логин:</td><td class=data2 align=left><input type=text class=text name=login size=40 value="<?php echo CutQuots($Login)?>"></td></tr>
<tr><td align=right class=data1><input type=checkbox name=changepassword<?php echo ($Action=='update')?'':' checked disabled'?>> - изменить пароль:</td><td class=data1 align=left><input type=password class=text name=password size=40></td></tr>
<input type=hidden name=passwordhash value="<?php echo CutQuots($Password)?>">
<tr><td align=right class=data2>E-Mail:</td><td class=data2 align=left><input type=text class=text name=email size=40 value="<?php echo CutQuots($EMail)?>"></td></tr
<tr><td align=right class=data1>Реальное имя:</td><td class=data1 align=left><input type=text class=text name=name size=40 value="<?php echo CutQuots($Name)?>"></td></tr>
<tr><td align=right class=data2>Описание:</td><td class=data2 align=left><textarea name=description cols=50 rows=6><?php echo CutQuots($Description)?></textarea></td></tr>
<tr><td align=center class=colheader colspan=2>Права доступа</td></tr>
<?
	if($SessionSettings['suppresshelp']!=1)echo '<tr><td class=data1 colspan=2><p class=main>Ниже приведён список возможных глобальных прав доступа модератора. Расставьте необходимые права доступа модератору. Кроме глобальных прав доступа, выставляемых в этом разделе, можно определять права доступа на каждый их узлов дерева сайта.</p></td></tr>';
?>
<tr><td align=left class=data2 colspan=2>
<?php
	foreach($permTypes as $aKey => $aPerm)
		if($aKey!='developer'){
			$checked=(UserHaveAccess($aKey,$UserPermitions))?' checked':'';
			echo '<input type=checkbox' . $checked . ' name=perms' . $aKey . '> - ' . $aPerm[1] . '<br>';
		};

?>
</td></tr>
<tr><td class=data1 align=center colspan=2><?php echo ($Action=='update')?'<input type=checkbox name=deleteuser>-удалить&nbsp;':''?><input type=submit class=button value="<?php echo ($Action=='update')?'Обновить':'Добавить'?>"></td></tr></form>
<?php echo drwTableEnd() . '<br>' . drwTableBegin('100%',0);?>
<tr><td colspan=5 class=header align=center>существующие модераторы</td></tr>
<tr>
<td class=colheader align=center>Логин</td>
<td class=colheader align=center>EMail</td>
<td class=colheader align=center>Права доступа</td>
<td class=colheader align=center>&nbsp;</td>
</tr>
<?php
	$sql='select `id`, `login`, `permitions`, `email` from `moderators` order by `id`';
	$db->sql_query($sql);
	$tdclass='data1';
	while($row=$db->sql_fetchrow()){
		$tdclass=($tdclass=='data1')?'data2':'data1';
		echo '<form method=post><input type=hidden name=action value="edit"><input type=hidden name=id value="' . $row['id'] . '"><tr>';
		echo '<td class=' . $tdclass . ' align=left>' . CutQuots($row['login']) . '</td>';
		echo '<td class=' . $tdclass . ' align=left>' . CutQuots($row['email']) . '</td>';
		echo '<td class=' . $tdclass . ' align=left>' . ShowAccessRights($row['permitions']) . '</td>';
		echo '<td class=' . $tdclass . ' align=center><input type=submit class=button value="менять"></td>';
		echo '</tr></form>';
	};
    echo drwTableEnd() . '<br>';
    include("bottom.php");
?>