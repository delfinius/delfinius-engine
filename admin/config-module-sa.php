<?php
	include ("common-admin.php");
	$Result='';
	$TheModule=$HTTP_GET_VARS['module'];
	if(!HaveAccess('params'))header('Location : index.php');
	$ModuleParamsFormPrefix="<form method=post enctype=\"multipart/form-data\" action=\"config-module-sa.php?module=$TheModule\">";
	if($HTTP_POST_VARS["action"]=="mainparamsupdate"){
		$NeedToSaveConfig=ConfigFromCollection($HTTP_POST_VARS);
		if(isset($SAmodsArray[$TheModule])){
			$NeedToSaveConfig=MergeConfigs($SAmodsArray[$TheModule]->prms,$NeedToSaveConfig);
			SaveConfig(0,$TheModule,$NeedToSaveConfig);
		};
	};
	if($SAmodsArray[$TheModule]){
		$ConfigToEdit=MergeConfigs($SAmodsArray[$TheModule]->prms,GetConfig(0,$TheModule));
		$EditorHeader="�������� ��������� ������ \"" . CutQuots($SAmodsArray[$TheModule]->realname) . "\"";
	}else{
		header('Location: .');
	};
	include ("top.php");
	
	include ($site_root . './define/configformchecker.php');

	if($SAmodsArray[$TheModule]){
		$AdminOutput=$SAmodsArray[$TheModule]->MakeParamsOuput($ModuleParamsFormPrefix);
		if($AdminOutput!=''){
			echo drwTableBegin('100%',0) . "<tr><td class=header align=left>�������������� ��������� ������: \"" . CutQuots($SAmodsArray[$TheModule]->realname) . "\"</td></tr><tr><td class=data1>";
			echo $AdminOutput . "</td></tr>" . drwTableEnd() . "<br>";
		};
	};

	$ConfigForm=drwTableBegin('100%',0) . '<form method=post enctype="multipart/form-data" onsubmit="return checkParamsForm(this);" action="config-module-sa.php?module=' . $TheModule . '"><input type=hidden name=action value="mainparamsupdate">';
	$ConfigForm.='<tr><td class=header colspan=4>' . $EditorHeader . '</td></tr>';
	if($SessionSettings['suppresshelp']!=1)$ConfigForm.='<tr><td class=data1 colspan=4><p class=main>��� ������ ���� ����� �� ������ �������������� ��������� ����������� "�� ���������" ��� �������� �����. � ������ ������� ������� ��������� ������� - ������� ��������������� ���������. ��� ���� ����� �������� �������� ��������� - ���������� ��������� ������ � �������� �������� ��������� �� ��������. � ������ ���� �� ������ ������� �������� � �������� �������� - ���������� ����� ������ "���."</p></td></tr>';
	$ConfigForm.=ConfigToForm($ConfigToEdit);
	$ConfigForm.='<tr><td class=data2 align=center colspan=4><input type=submit class=button value="�������� ���������"></td></tr></form>'.drwTableEnd();
	echo $ConfigForm . "<br>";

	include ("bottom.php");

?>