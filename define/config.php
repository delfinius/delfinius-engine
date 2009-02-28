<?php
class ConfigParam{
    var $Value;
    function ConfigParam($theName){
	$this->Name=$theName;
	$this->Description='';
	$this->DataType='';
	$this->Protected=false;
	$this->Overrided=false;
    }
};

function GetConfig($theNode, $theModule){
	global $db;
	$retVal=array();
	$sql="select `name`, `description`, `datatype`, `int`, `float`, `char`, `memo`, `bool`, `protected` from `config` where `node`=" . $theNode . " and `mod`='$theModule' order by `node` ,`name`";
	if(!$db->sql_query($sql)){
//	    $sqlerror=$db->sql_error();
//	    die($sqlerror['message']);
	};
	$theParamName='';
	while($row=$db->sql_fetchrow()){
		$theParamName=$row['name'];
		if(isset($retVal[$theParamName]))unset($retVal[$theParamName]);
		$retVal[$theParamName]=new ConfigParam($theParamName);
		$retVal[$theParamName]->Description=$row['description'];
		$retVal[$theParamName]->DataType=$row['datatype'];
		switch($retVal[$theParamName]->DataType){
			case 'bool':{
				$retVal[$theParamName]->Value=($row['bool']==1);
				break;
			};
			case 'memo':{
				$retVal[$theParamName]->Value=$row['memo'];
				break;
			};
			default:{
				$retVal[$theParamName]->Value=$row[$retVal[$theParamName]->DataType];
			};
		};
		$retVal[$theParamName]->Protected=($row['Protected']==1);
		$retVal[$theParamName]->Overrided=false;
	};
	return $retVal;
};

function MergeConfigs($theSrcConfig, $theOverrideConfig){
	$retVal=array();
	foreach($theSrcConfig as $aLocalKey => $localParam)
		if($theOverrideConfig[$aLocalKey]){
			$retVal[$aLocalKey]=$theOverrideConfig[$aLocalKey];
			$retVal[$aLocalKey]->Overrided=true;
		}else{
			$retVal[$aLocalKey]=$theSrcConfig[$aLocalKey];
			$retVal[$aLocalKey]->Overrided=false;
		};
	return $retVal;
};

function SaveConfig($theNode, $theModule, $theConfig){
	global $db;
	foreach($theConfig as $localKey => $aConfigParam)
		if($aConfigParam->Overrided){
			$datavalue='';
			switch($aConfigParam->DataType){
				case 'bool':{
					$datavalue=($aConfigParam->Value)?'1':'0';
					break;
				};
				case 'memo':{
					$datavalue="'" . str_replace("'","''",$aConfigParam->Value) . "'";
					break;
				};
				case 'char':{
					$datavalue="'" . str_replace("'","''",$aConfigParam->Value) . "'";
					break;
				};
				default:{
					$datavalue=$aConfigParam->Value;
				};
			};
			$sql="select `name` from `config` where `node`=$theNode and `mod`='$theModule' and `name`='" . $aConfigParam->Name . "'";
			$db->sql_query($sql);
			if($db->sql_numrows()==0){
				$valprotected=($aConfigParam->Protected)?'1':'0';
				$sql="insert into `config` (`node`, `mod`, `" . $theConfig[$localKey]->DataType . "`, `name`, `description`, `datatype`, `protected`) values ($theNode, '$theModule', $datavalue , '" . str_replace("'", "''", $aConfigParam->Name) . "' , '" . str_replace("'", "''", $aConfigParam->Description) . "', '" . str_replace("'", "''", $aConfigParam->DataType) . "', $valprotected)";
			}else{
				$sql="update `config` set `" . $aConfigParam->DataType . "`=$datavalue where `node`=$theNode and `mod`='$theModule' and `name`='" . $aConfigParam->Name . "'";
			};
			$db->sql_query($sql);
		}else{
			if(!$aConfigParam->Protected){
				$db->sql_query("delete from `config` where `node`=$theNode and `mod`='$theModule' and `name`='" . $aConfigParam->Name . "'");
			}else if(HaveAccess('developer')){
				$db->sql_query("delete from `config` where `node`=$theNode and `mod`='$theModule' and `name`='" . $aConfigParam->Name . "'");
			};
		};
};

function ConfigParamToForm($theParam,$ParamSuffix){
	$retVal='<tr><td class=data1 align=center><input type=hidden name="paramname' . $ParamSuffix . '" value="' . $theParam->Name . '"><input type=hidden name="paramtype' . $ParamSuffix . '" value="' . $theParam->DataType .'">';
	$retVal.='<input type=hidden name="paramdescription' . $ParamSuffix . '" value="' . CutQuots($theParam->Description) . '"><input type=hidden name="paramprotected' . $ParamSuffix . '" value="' . (($theParam->Protected)?'on':'off') . '">';
	$AlreadyOverrided=($theParam->Overrided)?' checked':'';
	$retVal.='<input type=checkbox name="override' . $ParamSuffix . '"' . $AlreadyOverrided . '></td>';
	$onchangeEvent='';
//	$onchangeEvent=" onkeydown=\"this.form.override" . $ParamSuffix . ".checked=true;\" onclick=\"this.form.override" . $ParamSuffix . ".checked=true;\"";
	switch($theParam->DataType){
		case 'int':{
			$retVal.='<td class=data1><strong><u>' . $theParam->Name . '</u></strong></td><td class=data1>' . CutQuots($theParam->Description) . '</td><td class=data1 align=right><input type=text class=text name="paramvalue' . $ParamSuffix . '" style="text-align:right;" size=10 value="' . fmtInt($theParam->Value) . '"' . $onchangeEvent . '></td></tr>';
			break;
		};
		case 'float':{
			$retVal.='<td class=data1><strong><u>' . $theParam->Name . '</u></strong></td><td class=data1>' . CutQuots($theParam->Description) . '</td><td class=data1 align=right><input type=text class=text name="paramvalue' . $ParamSuffix . '" style="text-align:right;" size=10 value="' . fmtFloat($theParam->Value) . '"' . $onchangeEvent . '></td></tr>';
			break;
		};
		case 'char':{
			$retVal.='<td class=data1><strong><u>' . $theParam->Name . '</u></strong></td><td class=data1>' . CutQuots($theParam->Description) . '</td><td class=data1>&nbsp;</td>';
			$retVal.='<tr><td class=data1>&nbsp;</td><td class=data1 align=left colspan=3><input type=text class=text name="paramvalue' . $ParamSuffix . '" style="text-align:left;" size=100 maxlength=250 value="' . CutQuots($theParam->Value) . '"' . $onchangeEvent . '></td></tr>';
			break;
		};
		case 'memo':{
			$retVal.='<td class=data1><strong><u>' . $theParam->Name . '</u></strong></td><td class=data1>' . CutQuots($theParam->Description) . '</td><td class=data1>&nbsp;</td>';
			$retVal.='<tr><td class=data1>&nbsp;</td><td class=data1 align=left colspan=3><textarea name="paramvalue' . $ParamSuffix . '" cols=100 rows=7' . $onchangeEvent . '>' . CutQuots($theParam->Value) . '</textarea></td></tr>';
			break;
		};
		case 'bool':{
			$checked=($theParam->Value)?' checked':'';
			$retVal.='<td class=data1><strong><u>' . $theParam->Name . '</u></strong></td><td class=data1>' . CutQuots($theParam->Description) . '</td><td class=data1 align=right><input type=checkbox name="paramvalue' . $ParamSuffix . '"' . $checked . $onchangeEvent . '></td></tr>';
			break;
		};
		default:
	};
	return $retVal;
};

function ConfigToForm($theConfig){
	$retVal='<tr><td class=colheader align=center>вкл.</td><td class=colheader>название</td><td class=colheader>описание</td><td class=colheader>&nbsp;</td></tr>';
	$localCounter=0;
	foreach($theConfig as $localKey => $localParam){
		$localCounter++;
		if(!$localParam->Protected){
			$retVal.=ConfigParamToForm($localParam,$localCounter);
		}else if(HaveAccess('developer')){
			$retVal.=ConfigParamToForm($localParam,$localCounter);
		};
	};
	$retVal.='<input type=hidden name=paramscount value=' . $localCounter . '>';
	return $retVal;
};

function ConfigFromCollection($theCollection){
	$retVal=array();
	$ParamsCount=$theCollection['paramscount'];
	for($localcounter=1;$localcounter<=$ParamsCount;$localcounter++)
		if($theCollection['override' . $localcounter]=='on'){
			$theParamName=stripslashes($theCollection['paramname' . $localcounter]);
			$retVal[$theParamName]=new ConfigParam($theParamName);
			$retVal[$theParamName]->Description=stripslashes($theCollection['paramdescription' . $localcounter]);
			$retVal[$theParamName]->Protected=($theCollection['paramprotected' . $localcounter]=='on');
			$retVal[$theParamName]->DataType=stripslashes($theCollection['paramtype' . $localcounter]);
			switch($retVal[$theParamName]->DataType){
				case 'bool':{
					$retVal[$theParamName]->Value=($theCollection['paramvalue' . $localcounter]=='on');
					break;
				};
				case 'int':{
					$retVal[$theParamName]->Value=stripslashes($theCollection['paramvalue' . $localcounter]);
					break;
				};
				case 'float':{
					$retVal[$theParamName]->Value=stripslashes($theCollection['paramvalue' . $localcounter]);
					break;
				};
				default:{
					$retVal[$theParamName]->Value=stripslashes($theCollection['paramvalue' . $localcounter]);
				};
			};
		};
	return $retVal;
};




function initCoreParams(){
	global $SiteMainURL;
	$retprms = array();
	$retprms["fulldeletenode"]=new ConfigParam("fulldeletenode");
	$retprms["fulldeletenode"]->Description="ѕолное удаление раздела. ≈сли включено, то при удалении раздела будет удалена всинформаци€ о разделе без возможности восстановлени€. ¬ противном случае раздел потер€ет \"родител€\" и окажетс€ недоступным в дереве сайта.";
	$retprms["fulldeletenode"]->DataType="bool";
	$retprms["fulldeletenode"]->Value=false;
	$retprms["fulldeletenode"]->Protected=true;

	$retprms["anticache"]=new ConfigParam("anticache");
	$retprms["anticache"]->Description="»спользовать случайное число в ссылках дл€ предотвращени€ обращений к кешированным html-документам.";
	$retprms["anticache"]->DataType="bool";
	$retprms["anticache"]->Value=false;
	$retprms["anticache"]->Protected=true;

	$retprms["startnode"]=new ConfigParam("startnode");
	$retprms["startnode"]->Description="»дентификатор раздела сайта, открывающегос€ при заходе на главную страницу.";
	$retprms["startnode"]->DataType='int';
	$retprms["startnode"]->Value=1;
	$retprms["startnode"]->Protected=false;

	$retprms["mapnode"]=new ConfigParam("mapnode");
	$retprms["mapnode"]->Description="»дентификатор раздела сайта, отображающего карту сайта.";
	$retprms["mapnode"]->DataType='int';
	$retprms["mapnode"]->Value=1;
	$retprms["mapnode"]->Protected=false;

	$retprms["webmasteremail"]=new ConfigParam("webmasteremail");
	$retprms["webmasteremail"]->Description="јдрес электронной почты web-мастера.";
	$retprms["webmasteremail"]->DataType='char';
	$retprms["webmasteremail"]->Value='webmaster@' . $SiteMainURL;
	$retprms["webmasteremail"]->Protected=false;

	$retprms["installed"]=new ConfigParam("installed");
	$retprms["installed"]->Description="‘лаг установленности сайта (наличие необходимых структур в Ѕƒ).";
	$retprms["installed"]->DataType="bool";
	$retprms["installed"]->Value=false;
	$retprms["installed"]->Protected=true;

    return $retprms;
};

$coreParams=initCoreParams();
$coreParams=MergeConfigs($coreParams,GetConfig(0,'core'));

?>