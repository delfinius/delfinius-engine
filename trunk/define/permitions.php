<?php
// Константы - уровни доступа к сайту
$permTypes=array();
$permTypes["login"]=array(2,"Вход на страницу управления");
$permTypes["bigadmin"]=array(4,"Управление содержимым всех разделов (создание/удаление/изменение)");
$permTypes["root"]=array(1,"Управление модераторами");
$permTypes["params"]=array(16,"Изменение параметров работы сайта и модулей");
$permTypes["developer"]=array(32768,"разработчик сайта");
$permTypes["accessadmin"]=array(32,"Изменение прав доступа ко всем разделам сайта");
$permTypes["templateseditor"]=array(8,"Редактирование шаблонов");
$permTypes["mainbaneradmin"]=array(64,"Изменение банера главной страницы");


$NodeAccessFields=array();
$NodeAccessFields["contentaccess"]="менять содержание раздела";
$NodeAccessFields["paramsaccess"]="менять название, сортировку, доступность раздела";
$NodeAccessFields["permitionsaccess"]="менять права доступа к разделу";
$NodeAccessFields["makesubnodesaccess"]="создавать дочерние разделы";


// Функция проверки прав доступа пользователя

function HaveAccess($accessType){
	global $SessionSettings;
	global $permTypes;
	if(!$permTypes[$accessType])return false;
	$numAccessType=$permTypes[$accessType][0];
	$Permitions=$SessionSettings['permitions'];
	$retval=(($Permitions & $numAccessType)==$numAccessType);
	return $retval;
};

// Функция проверки прав доступа пользователя к узлу дерева

function HaveNodeAccess($theNode,$accessField){
	global $SessionSettings, $db;
	$sql="select node from `cross-moderators` where node=$theNode and moderator=" . $SessionSettings['id'] . " and $accessField=1";
	if(!$db->sql_query($sql))return false;
	return ($db->sql_numrows()>0);
};

// Функция проверки прав доступа любого из пользователей

function UserHaveAccess($accessType,$UserPermitions){
	global $permTypes;
	if(!$permTypes[$accessType])return false;
	$numAccessType=$permTypes[$accessType][0];
	$Permitions=$UserPermitions;
	$retval=(($Permitions & $numAccessType)==$numAccessType);
	return $retval;
};

function CheckAccess($login,$password){
	$retval=-1;
	global $db;
	global $SessionSettings;
	$sql="select id, permitions, login, password, email, suppresshelp from moderators where login='" . $login . "' and password='" . md5($password) . "'";
	if(!$db->sql_query($sql)){
//	    $sqlerror = $db->sql_error();
//	    die($sqlerror['message']);
	};
	if($db->sql_numrows()==0)return $retval;
	$row=$db->sql_fetchrow();
	$SessionSettings['id']=$row['id'];
	$SessionSettings['login']=$row['login'];
	$SessionSettings['permitions']=$row['permitions'];
	$SessionSettings['suppresshelp']=$row['suppresshelp'];
	$retval=0;
	WriteSessionSettings();
	return $retval;
};

function ShowAccessRights($aRights){
	global $permTypes;
	$retVal='';
	foreach($permTypes as $aKey => $aPermType){
		if(($permTypes[$aKey][0] & $aRights)==$permTypes[$aKey][0])$retVal.=$permTypes[$aKey][1] . ', ';
	};
	$retVal=substr($retVal,0,strlen($retVal)-2);
	return $retVal;
};

?>