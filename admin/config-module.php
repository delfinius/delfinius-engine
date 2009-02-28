<?php
	include ("common-admin.php");
	$Result='';
	$TheModule=$HTTP_GET_VARS['module'];
	if(!HaveAccess('params'))header('Location : index.php');
	$ModuleParamsFormPrefix='<form method=post enctype="multipart/form-data" action="config-module.php?module=' . $TheModule . '">';
	if($HTTP_POST_VARS['action']=='mainparamsupdate'){
		$NeedToSaveConfig=ConfigFromCollection($HTTP_POST_VARS);
		if($TheModule=='core'){
			$NeedToSaveConfig=MergeConfigs($coreParams,$NeedToSaveConfig);
			SaveConfig(0,$TheModule,$NeedToSaveConfig);
			$coreParams=initCoreParams();
			$coreParams=MergeConfigs($coreParams,GetConfig(0,'core'));
		}else if(isset($modsArray[$TheModule])){
			$NeedToSaveConfig=MergeConfigs($modsArray[$TheModule]->prms,$NeedToSaveConfig);
			SaveConfig(0,$TheModule,$NeedToSaveConfig);
		};
	};
	if($TheModule=='core'){
		$ConfigToEdit=$coreParams;
		$EditorHeader='основные параметры сайта';
	}else if($modsArray[$TheModule]){
		$ConfigToEdit=MergeConfigs($modsArray[$TheModule]->prms,GetConfig(0,$TheModule));
		$EditorHeader='основные параметры модуля "' . CutQuots($modsArray[$TheModule]->realname) . '"';
	}else{
		header('Location: .');
	};
	include ("top.php");
	
	include ($site_root . './define/configformchecker.php');

	$ConfigForm=drwTableBegin('100%',0) . '<form method=post enctype="multipart/form-data" onsubmit="return checkParamsForm(this);" action="config-module.php?module=' . $TheModule . '"><input type=hidden name=action value="mainparamsupdate">';
	$ConfigForm.='<tr><td class=header colspan=4>' . $EditorHeader . '</td></tr>';
	if($SessionSettings['suppresshelp']!=1)$ConfigForm.='<tr><td class=data1 colspan=4><p class=main>При помощи этой формы вы можете переопределить параметры назначенные "по умолчанию" при создании сайта. В первом столбце таблицы находится галочка - признак переопределения параметра. Для того чтобы изменить значение параметра - необходимо поставить крыжик и изменить значение параметра на желаемое. В случае если вы хотите вернуть параметр в исходное значение - достаточно снять крыжик "вкл."</p></td></tr>';
	$ConfigForm.=ConfigToForm($ConfigToEdit);
	$ConfigForm.='<tr><td class=data2 align=center colspan=4><input type=submit class=button value="обновить параметры"></td></tr></form>'.drwTableEnd();
	echo $ConfigForm . "<br>";

	if($modsArray[$TheModule]){
		$AdminOutput=$modsArray[$TheModule]->MakeParamsOuput($ModuleParamsFormPrefix);
		if($AdminOutput!=''){
			echo '<br>' . drwTableBegin('100%',0) . '<tr><td class=header align=left>дополнительные параметры модуля: "' . CutQuots($modsArray[$TheModule]->realname) . '"</td></tr><tr><td class=data1>';
			echo $AdminOutput . '</td></tr>' . drwTableEnd();
		};
	};
    include ("bottom.php");

?>