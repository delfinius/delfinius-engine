<?php

/////////////////////////////////////////////////////////////////////
//	Первый модуль - проверка возможности подключения
/////////////////////////////////////////////////////////////////////

class clsPlanetaAbilityModule extends clsModule{
	function clsPlanetaAbilityModule($modName,$modDName,$dbconnector){
		parent::clsModule($modName,$modDName,$dbconnector);
		$this->SearchAble=false;
		$this->version="1.0.1";
		$this->helpstring="<p>Модуль реализует механизм проверки возможности подключения по определённому адресу. Разработан только для КОМТЕХЦЕНТРа</p>";

		$this->prms["template"]=new ConfigParam("template");
		$this->prms["template"]->Description="Шаблон страницы. Допускаемые для замены значения: form, streetselect, houseinput";
		$this->prms["template"]->DataType="memo";
		$this->prms["template"]->Value="<table width=100% border=0 cellpadding=0 cellspacing=0><td width=10 valign=top>&nbsp;</td><td width=* valign=top>" . 
				"<p>Тут абзац с вступительным текстом....</p>" .
				"<center><table border=0 width=80% cellpadding=1 cellspacing=1><tr><td class=header align=center colspan=2>Введите свои данные</td></tr>--form--" .
				"<tr><td class=data1 align=right>Необходимое подключение: </td><td class=data1 align=left>--typeselector--</td></tr>" .
				"<tr><td class=data1 align=right>Улица, район: </td><td class=data1 align=left>--streetselect--</td></tr>" .
				"<tr><td class=data2 align=right>Дом: </td><td class=data2 align=left>--houseinput--</td></tr>" .
				"<tr><td class=data1 align=center colspan=2><input type=submit class=button value=\"проверить\"></td></tr></form></table>" .
				"</center></td><td width=10 valign=top>&nbsp;</td></tr></table>";
		$this->prms["template"]->Protected=false;

		$this->prms["result.template"]=new ConfigParam("result.template");
		$this->prms["result.template"]->Description="Шаблон для отображения результата проверки. Допускаемые для замены значения: items";
		$this->prms["result.template"]->DataType="memo";
		$this->prms["result.template"]->Value="<table width=100% border=0 cellpadding=0 cellspacing=0><td width=10 valign=top>&nbsp;</td><td width=* valign=top>" . 
				"<p>Тут абзац с вступительным текстом для вывода результатов проверки....</p>" .
				"<center><table border=0 width=80% cellpadding=1 cellspacing=1><tr><td class=header align=center>Район, улица</td><td class=header align=center>дом</td><td class=header align=center>статус</td></tr>" .
				"--items--" .
				"</table></center></td><td width=10 valign=top>&nbsp;</td></tr></table>";
		$this->prms["result.template"]->Protected=false;

		$this->prms["result.template.item"]=new ConfigParam("result.template.item");
		$this->prms["result.template.item"]->Description="Шаблон для отображения одной строки в таблице результатов проверки. Допускаемые для замены значения: area, street, house, status, itvstatus";
		$this->prms["result.template.item"]->DataType="memo";
		$this->prms["result.template.item"]->Value="<tr><td class=data1>--area--, --street--</td><td class=data2 align=center>--house--</td><td class=data1 align=center>--status--</td><td class=data1 align=center>--itvstatus--</td></tr>";
		$this->prms["result.template.item"]->Protected=false;

		$this->prms["result.template.itemdev"]=new ConfigParam("result.template.itemdev");
		$this->prms["result.template.itemdev"]->Description="html-текст разделяющий result.template.item";
		$this->prms["result.template.itemdev"]->DataType="char";
		$this->prms["result.template.itemdev"]->Value="";
		$this->prms["result.template.itemdev"]->Protected=false;

		$this->modTablePreamble="mod_planeta_";
		$this->tblAreas=$this->modTablePreamble . "areas";
		$this->tblStreets=$this->modTablePreamble . "streets";
		$this->tblStatus=$this->modTablePreamble . "status";
		$this->tblHouses=$this->modTablePreamble . "houses";
	
		$this->ableuseracts=array("check"=>"check","start"=>"start");
	}

	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
		$retVal="Данный модуль конфигурируется только глобально.";
		return $retVal;
	}

	function MakeUserOuput($theNode, $theFormPrefix){
		global $HTTP_POST_VARS, $HTTP_GET_VARS, $SessionSettings;
		$retVal=array();
		if(isset($HTTP_POST_VARS["mod_" . $this->name . "_action"])){
			$mod_action=$HTTP_POST_VARS["mod_" . $this->name . "_action"];
		}else{
			$mod_action=$HTTP_GET_VARS["mod_" . $this->name . "_action"];
		}
		if(!isset($this->ableuseracts[$mod_action]))$mod_action="start";
		if($mod_action=="start"){
			$sql="select `$this->tblStreets`.`id`, `$this->tblAreas`.`name` as `areaname`, `$this->tblStreets`.`name` as `streetname` from `$this->tblStreets` inner join `$this->tblAreas` on `$this->tblAreas`.`id`=`$this->tblStreets`.`area` order by `$this->tblStreets`.`name`, `$this->tblAreas`.`name`";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
			$streetselect="<select name=street size=1>";
			while($row=$this->dbc->sql_fetchrow()){
				$streetselect.="<option value=" . $row["id"] . ">" . $row["streetname"] . ", " . $row["areaname"];
			};
			$streetselect.="</select>";
			$page=$this->prms["template"]->Value;
			$page=str_replace("--form--","<form method=post action=\"$theFormPrefix\"><input type=hidden name=mod_" . $this->name . "_action value=check>",$page);
			$page=str_replace("--streetselect--",$streetselect,$page);
			$page=str_replace("--houseinput--","<input type=text class=text name=house size=20>",$page);
			$retVal[0]=$page;
		};
		if($mod_action=="check"){
			$sql="select `$this->tblStatus`.`name` as `status`, `$this->tblHouses`.`name` as `house`, `$this->tblStreets`.`name` as `street`, `$this->tblAreas`.`name` as `area`, `itvstatus`.`name` as `itvstatus` \n" .
					"from `$this->tblStatus`\n" .
					"inner join `$this->tblHouses` on `$this->tblHouses`.`status`=`$this->tblStatus`.`id`\n" .
					"inner join `$this->tblStreets` on `$this->tblStreets`.`id`=`$this->tblHouses`.`street`\n" .
					"inner join `$this->tblAreas` on `$this->tblAreas`.`id`=`$this->tblStreets`.`area`\n" .
					"inner join `$this->tblStatus` as `itvstatus` on `itvstatus`.`id`=`$this->tblHouses`.`itvstatus`\n" .
					"where `$this->tblStreets`.`id`=" . $HTTP_POST_VARS["street"] . " and `$this->tblHouses`.`name` like '%" . $HTTP_POST_VARS["house"] . "%' order by `$this->tblHouses`.`name`";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
			$items="";
			while($row=$this->dbc->sql_fetchrow()){
				$oneline=$this->prms["result.template.item"]->Value;
				foreach($row as $dkey => $ddata)
					$oneline=str_replace("--$dkey--",CutQuots($ddata),$oneline);
				$items.=$oneline . $this->prms["result.template.itemdev"]->Value;
			};
			if(strlen($items)>strlen($this->prms["result.template.itemdev"]->Value))$items=substr($items,0,(strlen($items)-strlen($this->prms["result.template.itemdev"]->Value)));
			$page=$this->prms["result.template"]->Value;
			$page=str_replace("--items--",$items,$page);
			$retVal[0]=$page;
		};
		return $retVal;
	}
	
	function MakeParamsOuput($theFormPrefix){
		global $HTTP_POST_VARS;
		$ablepages=array("areas"=>"районы", "streets"=>"улицы", "houses"=>"дома", "status"=>"статусы");
		$curactpage=$HTTP_POST_VARS["actiontarget"];
		if(!isset($ablepages[$curactpage]))$curactpage="areas";
		$retVal=drwTableBegin("100%","");
		$modifedform=substr($theFormPrefix,0,strlen($theFormPrefix)-1);
		$modifedform.=" name=mod_planeta_form>";
		$retVal.="$modifedform<input type=hidden name=actiontarget value=$curactpage></form>";
		$retVal.="<tr>";
		foreach($ablepages as $pagekey=>$pagename){
			if($pagekey==$curactpage){
				$retVal.="<td class=header align=center style=\"background-color:#FFFFFF;\"><a href=\"javascript:document.forms['mod_planeta_form'].actiontarget.value='$pagekey';document.forms['mod_planeta_form'].submit()\">$pagename</a></td>";
			}else{
				$retVal.="<td class=header align=center><a href=\"javascript:document.forms['mod_planeta_form'].actiontarget.value='$pagekey';document.forms['mod_planeta_form'].submit()\" class=white>$pagename</a></td>";
			};
		};
		$retVal.="</tr><tr><td class=border colspan=" . count($ablepages) . "><table width=100% border=0 cellpadding=1 cellspacing=1>";
		// Редкатирование районов
		if($curactpage=="areas"){
			$mod_action=$HTTP_POST_VARS["mod_planeta_action"];
			if($mod_action=="update"){
				$sql="update `$this->tblAreas` set `name`='" . $HTTP_POST_VARS["name"] . "' where `id`=" . $HTTP_POST_VARS["id"];
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror["message"]);
				};
			};
			if($mod_action=="delete"){
				$sql="delete from `$this->tblAreas` where `id`=" . $HTTP_POST_VARS["id"];
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror["message"]);
				};
			};
			if($mod_action=="insert"){
				$sql="insert into `$this->tblAreas` (`name`) values ('" . $HTTP_POST_VARS["name"] . "')";
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror["message"]);
				};
			};
			$sql="select `$this->tblAreas`.`id`, `$this->tblAreas`.`name` from `$this->tblAreas` order by `name`";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
			$tdclass="data1";
			while($row=$this->dbc->sql_fetchrow()){
				$retVal.="<tr>";
				$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=update>";
				$retVal.="<td class=$tdclass><input type=text size=80 class=text name=name value=\"" . CutQuots($row["name"]) . "\"></td>";
				$retVal.="<td class=$tdclass align=center><input type=submit class=button value=\"обновить\"></td></form>";
				$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=delete>";
				$retVal.="<td class=$tdclass align=center><input type=submit class=button value=\"удалить\"></td></form>";
				$tdclass=($tdclass=="data1")?"data2":"data1";
			};
			$retVal.="<tr><td colspan=3 class=colheader align=center>добавить запись</td></tr>";
			$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=insert>";
			$retVal.="<tr><td class=$tdclass><input type=text class=text name=name size=80></td><td colspan=2 align=center><input type=submit class=button value=\"добавить\"></td></tr></form>";
		};

		// Редкатирование возможных статусов
		if($curactpage=="status"){
			$mod_action=$HTTP_POST_VARS["mod_planeta_action"];
			if($mod_action=="update"){
				$sql="update `$this->tblStatus` set `name`='" . $HTTP_POST_VARS["name"] . "' where `id`=" . $HTTP_POST_VARS["id"];
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror["message"]);
				};
			};
			if($mod_action=="delete"){
				$sql="delete from `$this->tblStatus` where `id`=" . $HTTP_POST_VARS["id"];
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror["message"]);
				};
			};
			if($mod_action=="insert"){
				$sql="insert into `$this->tblStatus` (`name`) values ('" . $HTTP_POST_VARS["name"] . "')";
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror["message"]);
				};
			};
			$sql="select `$this->tblStatus`.`id`, `$this->tblStatus`.`name` from `$this->tblStatus` order by `name`";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
			$tdclass="data1";
			while($row=$this->dbc->sql_fetchrow()){
				$retVal.="<tr>";
				$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=update>";
				$retVal.="<td class=$tdclass><input type=text size=80 class=text name=name value=\"" . CutQuots($row["name"]) . "\"></td>";
				$retVal.="<td class=$tdclass align=center><input type=submit class=button value=\"обновить\"></td></form>";
				$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=delete>";
				$retVal.="<td class=$tdclass align=center><input type=submit class=button value=\"удалить\"></td></form>";
				$tdclass=($tdclass=="data1")?"data2":"data1";
			};
			$retVal.="<tr><td colspan=3 class=colheader align=center>добавить запись</td></tr>";
			$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=insert>";
			$retVal.="<tr><td class=$tdclass><input type=text class=text name=name size=80></td><td colspan=2 align=center><input type=submit class=button value=\"добавить\"></td></tr></form>";
		};

		// Редкатирование улиц
		if($curactpage=="streets"){
			$mod_action=$HTTP_POST_VARS["mod_planeta_action"];
			$currentarea=$HTTP_POST_VARS["area"];
			$sql="select `$this->tblAreas`.`id`, `$this->tblAreas`.`name` from `$this->tblAreas` order by `name`";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
			$areas=$this->dbc->sql_fetchrowset();
			$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=show>";
			$retVal.="<tr><td class=data2 align=center><select name=area>";
			$found=false;
			foreach($areas as $area){
				$selected=($currentarea==$area["id"])?" selected":"";
				if(strlen($selected)>0)$found=true;
				$retVal.="<option value=" . $area["id"] . "$selected>" . CutQuots($area["name"]);
			};
			if(!$found)$currentarea=$areas[0]["id"];
			$retVal.="</select></td><td colspan=3 align=center class=data2><input type=submit class=button value=\"выбрать\"></td></tr></form>";
			if($mod_action=="update"){
				$sql="update `$this->tblStreets` set `name`='" . $HTTP_POST_VARS["name"] . "', `area`=" . $HTTP_POST_VARS["area"] . " where `id`=" . $HTTP_POST_VARS["id"];
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror["message"]);
				};
			};
			if($mod_action=="delete"){
				$sql="delete from `$this->tblStreets` where `id`=" . $HTTP_POST_VARS["id"];
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror["message"]);
				};
			};
			if($mod_action=="insert"){
				$sql="insert into `$this->tblStreets` (`name`, `area`) values ('" . $HTTP_POST_VARS["name"] . "', " . $HTTP_POST_VARS["area"] . ")";
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror["message"]);
				};
			};
			$sql="select `$this->tblStreets`.`id`, `$this->tblStreets`.`name`, `$this->tblStreets`.`area` from `$this->tblStreets` where `$this->tblStreets`.`area`=$currentarea order by `name`";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
			$tdclass="data1";
			while($row=$this->dbc->sql_fetchrow()){
				$retVal.="<tr>";
				$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=update>";
				$retVal.="<td class=$tdclass><input type=text size=80 class=text name=name value=\"" . CutQuots($row["name"]) . "\"></td>";
				$retVal.="<td class=$tdclass><select name=area>";
				foreach($areas as $area){
					$selected=($row["area"]==$area["id"])?" selected":"";
					$retVal.="<option value=" . $area["id"] . "$selected>" . CutQuots($area["name"]);
				};
				$retVal.="</select></td>";
				$retVal.="<td class=$tdclass align=center><input type=submit class=button value=\"обновить\"></td></form>";
				$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=delete>";
				$retVal.="<td class=$tdclass align=center><input type=submit class=button value=\"удалить\"></td></form>";
				$tdclass=($tdclass=="data1")?"data2":"data1";
			};
			$retVal.="<tr><td colspan=4 class=colheader align=center>добавить запись</td></tr>";
			$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=insert>";
			$retVal.="<tr><td class=$tdclass><input type=text class=text name=name size=80></td>";
			$retVal.="<td class=$tdclass><select name=area>";
			foreach($areas as $area){
				$selected=($currentarea==$area["id"])?" selected":"";
				$retVal.="<option value=" . $area["id"] . "$selected>" . CutQuots($area["name"]);
			};
			$retVal.="</select></td>";
			$retVal.="<td colspan=2 align=center><input type=submit class=button value=\"добавить\"></td></tr></form>";
		};
		
		// Редкатирование домов
		if($curactpage=="houses"){
			$mod_action=$HTTP_POST_VARS["mod_planeta_action"];
			$currentstreet=$HTTP_POST_VARS["street"];
			$sql="select `$this->tblAreas`.`name` as `areaname`, `$this->tblStreets`.`name` as `name`, `$this->tblStreets`.`id` from `$this->tblAreas` inner join `$this->tblStreets` on `$this->tblStreets`.`area`=`$this->tblAreas`.`id` order by `$this->tblAreas`.`name`, `$this->tblStreets`.`name`";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
			$streets=$this->dbc->sql_fetchrowset();
			$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=show>";
			$retVal.="<tr><td class=data2 align=center><select name=street>";
			$found=false;
			foreach($streets as $street){
				$selected=($currentstreet==$street["id"])?" selected":"";
				if(strlen($selected)>0)$found=true;
				$retVal.="<option value=" . $street["id"] . "$selected>" . CutQuots($street["areaname"]) . ", " . CutQuots($street["name"]);
			};
			if(!$found)$currentstreet=$streets[0]["id"];
			$retVal.="</select></td><td colspan=3 align=center class=data2><input type=submit class=button value=\"выбрать\"></td></tr></form>";
	
			$sql="select `$this->tblStatus`.`id`, `$this->tblStatus`.`name` from `$this->tblStatus` order by `$this->tblStatus`.`name`";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
			$statuses=$this->dbc->sql_fetchrowset();
	
			if($mod_action=="update"){
				$sql="update `$this->tblHouses` set `name`='" . $HTTP_POST_VARS["name"] . "', `status`=" . $HTTP_POST_VARS["status"] . ", `itvstatus`=" . $HTTP_POST_VARS["itvstatus"] . " where `id`=" . $HTTP_POST_VARS["id"];
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror["message"]);
				};
			};
			if($mod_action=="delete"){
				$sql="delete from `$this->tblHouses` where `id`=" . $HTTP_POST_VARS["id"];
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror["message"]);
				};
			};
			if($mod_action=="insert"){
				$sql="insert into `$this->tblHouses` (`name`, `street`, `status`, `itvstatus`) values ('" . $HTTP_POST_VARS["name"] . "', " . $HTTP_POST_VARS["street"] . ", " . $HTTP_POST_VARS["status"] . ",  " . $HTTP_POST_VARS["itvstatus"] . ")";
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror["message"]);
				};
			};
			$sql="select `$this->tblHouses`.`id`, `$this->tblHouses`.`name`, `$this->tblHouses`.`street`, `$this->tblHouses`.`status`, `$this->tblHouses`.`itvstatus` from `$this->tblHouses` where `$this->tblHouses`.`street`=$currentstreet order by `name`";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
			$tdclass="data1";
			while($row=$this->dbc->sql_fetchrow()){
				$retVal.="<tr>";
				$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=update>";
				$retVal.="<td class=$tdclass><input type=text size=80 class=text name=name value=\"" . CutQuots($row["name"]) . "\"></td>";
				$retVal.="<td class=$tdclass><select name=status>";
				foreach($statuses as $status){
					$selected=($row["status"]==$status["id"])?" selected":"";
					$retVal.="<option value=" . $status["id"] . "$selected>" . CutQuots($status["name"]);
				};
				$retVal.="</select></td>";
				
				$retVal.="<td class=$tdclass><select name=itvstatus>";
				foreach($statuses as $status){
					$selected=($row["itvstatus"]==$status["id"])?" selected":"";
					$retVal.="<option value=" . $status["id"] . "$selected>" . CutQuots($status["name"]);
				};
				$retVal.="</select></td><input type=hidden name=street value=" . $row["street"] . ">";
				$retVal.="<td class=$tdclass align=center><input type=submit class=button value=\"обновить\"></td></form>";
				$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=delete>";
				$retVal.="<td class=$tdclass align=center><input type=submit class=button value=\"удалить\"></td></form>";
				$tdclass=($tdclass=="data1")?"data2":"data1";
			};
			$retVal.="<tr><td colspan=4 class=colheader align=center>добавить запись</td></tr>";
			$retVal.="$theFormPrefix<input type=hidden name=actiontarget value=$curactpage><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=mod_planeta_action value=insert>";
			$retVal.="<tr><td class=$tdclass><input type=text class=text name=name size=80></td>";
			$retVal.="<td class=$tdclass><select name=status>";
			foreach($statuses as $status){
				$retVal.="<option value=" . $status["id"] . "$selected>" . CutQuots($status["name"]);
			};
			$retVal.="</select></td>";
			$retVal.="<td class=$tdclass><select name=itvstatus>";
			foreach($statuses as $status){
				$retVal.="<option value=" . $status["id"] . "$selected>" . CutQuots($status["name"]);
			};
			$retVal.="</select></td><input type=hidden name=street value=$currentstreet>";
			$retVal.="<td colspan=2 align=center><input type=submit class=button value=\"добавить\"></td></tr></form>";
		};
		
		$retVal.="</table></td></tr>";
		$retVal.=drwTableEnd();
		return $retVal;
	}



	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->tblAreas`;
				CREATE TABLE `$this->tblAreas` (
				`id` INT NOT NULL AUTO_INCREMENT ,
				`name` VARCHAR( 150 ) NOT NULL ,
				PRIMARY KEY ( `id` )
			);
			DROP TABLE IF EXISTS `$this->tblStreets`;
			CREATE TABLE `$this->tblStreets` (
				`id` INT NOT NULL AUTO_INCREMENT ,
				`area` INT NOT NULL ,
				`name` VARCHAR( 150 ) NOT NULL ,
				PRIMARY KEY ( `id` )
			);
			DROP TABLE IF EXISTS `$this->tblStatus`;
			CREATE TABLE `$this->tblStatus` (
				`id` INT NOT NULL AUTO_INCREMENT ,
				`name` VARCHAR( 250 ) NOT NULL ,
				PRIMARY KEY ( `id` )
			);
			DROP TABLE IF EXISTS `$this->tblHouses`;
			CREATE TABLE `$this->tblHouses` (
				`id` INT NOT NULL AUTO_INCREMENT ,
				`street` INT NOT NULL ,
				`status` INT NOT NULL ,
				`name` VARCHAR( 50 ) NOT NULL ,
				PRIMARY KEY ( `id` )
			);";
		$splitedinstructions=split(";",$installsql);
		foreach($splitedinstructions as $oneinstruction)
			if($reinstall){
				$this->dbc->sql_query($oneinstruction);
			}else if(strstr($oneinstruction,"DROP TABLE")==FALSE){
					$this->dbc->sql_query($oneinstruction);
				};
	}

}


/////////////////////////////////////////////////////////////////////
//	Второй модуль - заявка на подключение
/////////////////////////////////////////////////////////////////////

class clsPlanetaOrderModule extends clsModule{
	function clsPlanetaOrderModule($modName,$modDName,$dbconnector){
		parent::clsModule($modName,$modDName,$dbconnector);
		$this->SearchAble=false;
		$this->version="1.0.1";
		$this->helpstring="<p>Модуль реализует механизм заявки на подключение к сети. Разработан только для КОМТЕХЦЕНТРа</p>";

		$this->prms["template"]=new ConfigParam("template");
		$this->prms["template"]->Description="Шаблон страницы. Допускаемые для замены значения: form, streetselect, houseinput";
		$this->prms["template"]->DataType="memo";
		$this->prms["template"]->Value="<table width=100% border=0 cellpadding=0 cellspacing=0><td width=10 valign=top>&nbsp;</td><td width=* valign=top>" . 
				"<p>Тут абзац с вступительным текстом....</p>" .
				"<center><table border=0 width=80% cellpadding=1 cellspacing=1><tr><td class=header align=center colspan=2>Введите свои данные</td></tr>--form--" .
				"<tr><td class=data1 align=right>Улица: </td><td class=data1 align=left>--streetselect--</td></tr>" .
				"<tr><td class=data2 align=right>Дом: </td><td class=data2 align=left>--houseinput--</td></tr>" .
				"<tr><td class=data1 align=right>Квартира: </td><td class=data1 align=left>--flatinput--</td></tr>" .
				"<tr><td class=data2 align=right>Фамилия И.О.: </td><td class=data2 align=left>--fioinput--</td></tr>" .
				"<tr><td class=data1 align=right>Эл. почта: </td><td class=data1 align=left>--emailinput--</td></tr>" .
				"<tr><td class=data2 align=right>Телефон: </td><td class=data2 align=left>--phoneinput--</td></tr>" .
				"<tr><td class=data1 align=right valign=top>Другая информация: </td><td class=data1 align=left>--otherinput--</td></tr>" .
				"<tr><td class=data2 align=center colspan=2><input type=submit class=button value=\"оставить заявку\"></td></tr></form></table>" .
				"</center></td><td width=10 valign=top>&nbsp;</td></tr></table>";
		$this->prms["template"]->Protected=false;

		$this->prms["response.template"]=new ConfigParam("response.template");
		$this->prms["response.template"]->Description="Шаблон страницы, отображаемой после того как была оставлена заявка.";
		$this->prms["response.template"]->DataType="memo";
		$this->prms["response.template"]->Value="<p class=main>Спасибо, ваша заявка принята! Если Вы указали корректную контактную информацию, наши специалисты с вами свяжутся.</p>";
		$this->prms["response.template"]->Protected=false;

		$this->prms["recipient.email"]=new ConfigParam("recipient.email");
		$this->prms["recipient.email"]->Description="e-mail на который будут приходить уведомления о новых заявках на подключение.";
		$this->prms["recipient.email"]->DataType="char";
		$this->prms["recipient.email"]->Value="welcome@planeta.tc";
		$this->prms["recipient.email"]->Protected=false;

		$this->modTablePreamble="mod_planeta_";
		$this->tblAreas=$this->modTablePreamble . "areas";
		$this->tblStreets=$this->modTablePreamble . "streets";
		$this->tblStatus=$this->modTablePreamble . "status";
		$this->tblHouses=$this->modTablePreamble . "houses";
		$this->tblOrders=$this->modTablePreamble . "orders";

		$this->ableuseracts=array("order"=>"order","start"=>"start","finished"=>"finished");
	}

	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
		$retVal="Данный модуль конфигурируется только глобально.";
		return $retVal;
	}

	function MakeUserOuput($theNode, $theFormPrefix){
		global $HTTP_POST_VARS, $HTTP_GET_VARS, $SessionSettings, $SiteMainURL;
		$retVal=array();
		if(isset($HTTP_POST_VARS["mod_" . $this->name . "_action"])){
			$mod_action=$HTTP_POST_VARS["mod_" . $this->name . "_action"];
		}else{
			$mod_action=$HTTP_GET_VARS["mod_" . $this->name . "_action"];
		};
		if(!isset($this->ableuseracts[$mod_action]))$mod_action="start";
		if($mod_action=="start"){
			$streetselect="<input type=text class=text name=street size=30>";
			$page=$this->prms["template"]->Value;
			$page=str_replace("--form--","<form method=post action=\"$theFormPrefix\"><input type=hidden name=mod_" . $this->name . "_action value=order>",$page);
			$page=str_replace("--streetselect--",$streetselect,$page);
			$page=str_replace("--houseinput--","<input type=text class=text name=house size=20>",$page);
			$page=str_replace("--flatinput--","<input type=text class=text name=flat size=20>",$page);
			$page=str_replace("--fioinput--","<input type=text class=text name=fio size=20>",$page);
			$page=str_replace("--emailinput--","<input type=text class=text name=email size=20>",$page);
			$page=str_replace("--phoneinput--","<input type=text class=text name=phone size=20>",$page);
			$page=str_replace("--otherinput--","<textarea name=other cols=30 rows=4></textarea>",$page);
			$page=str_replace("--typeselector--","<select name=typeselector><option value=\"Интернет\">Интернет<option value=\"Телевидение\">Телевидение<option value=\"Интернет+Телевидение\">Интернет+Телевидение</select>",$page);
			$retVal[0]=$page;
		};
		if($mod_action=="order"){
			$sql="insert into `$this->tblOrders` (`date`,`street`,`house`,`flat`,`fio`,`email`,`phone`,`other`) values (" . time() . ", '" . $HTTP_POST_VARS["street"] . "', '" . $HTTP_POST_VARS["house"] . "', '" . $HTTP_POST_VARS["flat"] . "', '" . $HTTP_POST_VARS["fio"] . "', '" . $HTTP_POST_VARS["email"] . "', '" . $HTTP_POST_VARS["phone"] . "','" . $HTTP_POST_VARS["other"] . "')";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
			$mailheader="MIME-Version: 1.0\nContent-type: text/plain; charset=windows-1251\nDate: " . gmdate('D, d M Y H:i:s', time()) . " UT\nX-Priority: 3\nX-MSMail-Priority: Normal\nX-Mailer: PHP\n";
			$mailheader.="From: " . $this->prms["recipient.email"]->Value . "<" . $this->prms["recipient.email"]->Value . ">\n";
			$message="На сайте $SiteMainURL была оставлена новая заявка на подключение.\n\n";
			$message.="Тип подключения: " . $HTTP_POST_VARS["typeselector"] . "\n";
			$message.="Улица: " . $HTTP_POST_VARS["street"] . "\n";
			$message.="Дом: " . $HTTP_POST_VARS["house"] . "\n";
			$message.="Квартира: " . $HTTP_POST_VARS["flat"] . "\n";
			$message.="ФИО: " . $HTTP_POST_VARS["fio"] . "\n";
			$message.="Телефон: " . $HTTP_POST_VARS["phone"] . "\n";
			$message.="e-mail: " . $HTTP_POST_VARS["email"] . "\n";
			$message.="Доп. информация: " . $HTTP_POST_VARS["other"] . "\n";
			mail($this->prms["recipient.email"]->Value,"новая заявка на подключение",$message,$mailheader);
			
			header("Location: $theFormPrefix&mod_" . $this->name . "_action=finished");
		};
		if($mod_action=="finished"){
			$retVal[0]=$this->prms["response.template"]->Value;
		};
		return $retVal;
	}
	
	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->tblOrders`;
				CREATE TABLE `$this->tblOrders` (
					`id` BIGINT NOT NULL AUTO_INCREMENT ,
					`date` INT NOT NULL ,
					`street` VARCHAR( 250 ) NOT NULL ,
					`house` VARCHAR( 30 ) NOT NULL ,
					`flat` VARCHAR( 250 ) NOT NULL ,
					`fio` VARCHAR( 250 ) NOT NULL ,
					`email` VARCHAR( 250 ) NOT NULL ,
					`phone` VARCHAR( 250 ) NOT NULL ,
					`other` TEXT NOT NULL ,
					PRIMARY KEY ( `id` )
				);";
		$splitedinstructions=split(";",$installsql);
		foreach($splitedinstructions as $oneinstruction)
			if($reinstall){
				$this->dbc->sql_query($oneinstruction);
			}else if(strstr($oneinstruction,"DROP TABLE")==FALSE){
					$this->dbc->sql_query($oneinstruction);
				};
	}
}


$thePlanetaAbilityModule=new clsPlanetaAbilityModule("planetaability","ПЛАНЕТА. Доступность подключения.",$db);
$modsArray["planetaability"]=$thePlanetaAbilityModule;

$thePlanetaOrderModule=new clsPlanetaOrderModule("planetaorder","ПЛАНЕТА. Заявка.",$db);
$modsArray["planetaorder"]=$thePlanetaOrderModule;
?>