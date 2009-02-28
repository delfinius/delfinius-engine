<?php
class clsContactsModule extends clsStandAloneModule{
	function clsContactsModule($modName,$modDName,$dbconnector){
		global $SessionSettings;
	 	parent::clsStandAloneModule($modName,$modDName,$dbconnector);
		$this->version="1.0.0";
		$this->helpstring="<p>Модуль реализующий отображение контактной информации в специальном слоте на сайте.</p>";
		$this->confTable="mod_contacts";
	}

	function MakeAdminOuput($theNode, $theFormPrefix){
		return '';
	}

	function MakeUserOuput($theNode, $theFormPrefix){
		return '';
	}

	function GetUserArray(){
		$retVal=array();
		$sql="select `id`, `contactname`, `contactdata`, `sort`, `visible` from `$this->confTable` where `visible`=1 order by `sort`";
		$this->dbc->sql_query($sql);
		$counter=0;
		while($row=$this->dbc->sql_fetchrow()){
			$retVal[$counter]=array($row["contactname"],$row["contactdata"]);
			$counter++;
		}
		return $retVal;
	}

	function MakeParamsOuput($theFormPrefix){
		global $HTTP_POST_VARS;
		$mod_action=$HTTP_POST_VARS["mod_contacts_action"];
		$visible=($HTTP_POST_VARS["visible"]=="on")?1:0;
		if($mod_action=="insert"){
			$sql="insert into `$this->confTable` (`contactname`, `contactdata`, `sort`, `visible`) values ('" . $HTTP_POST_VARS["contactname"] . "', '" . $HTTP_POST_VARS["contactdata"] . "', '" . $HTTP_POST_VARS["sort"] . "', $visible)";
			$this->dbc->sql_query($sql);
		};
		if($mod_action=="update"){
			$sql="update `$this->confTable` set  `contactname`='" . $HTTP_POST_VARS["contactname"] . "', `contactdata`='" . $HTTP_POST_VARS["contactdata"] . "', `sort`='" . $HTTP_POST_VARS["sort"] . "', `visible`=$visible where `id`=" . $HTTP_POST_VARS["id"];
			$this->dbc->sql_query($sql);
		};
		$id=0;
		$needact="insert";
		$contacname="";
		$contactdata="";
		$sort="0";
		$visible="";
		if($mod_action=="edit"){
			$sql="select `contactname`, `contactdata`, `sort`, `visible` from `$this->confTable`  where `id`=" . $HTTP_POST_VARS["id"];
			$this->dbc->sql_query($sql);
			if($row=$this->dbc->sql_fetchrow()){
				$id=$HTTP_POST_VARS["id"];
				$needact="update";
				$contactname=CutQuots($row["contactname"]);
				$contactdata=CutQuots($row["contactdata"]);
				$sort=$row["sort"];
				$visible=($row["visible"]==1)?" checked":"";
			};
		};
		$actionheader=($needact=="insert")?"добавить контакт":"редактировать контакт";
		$actionbtn=($needact=="insert")?"добавить":"обновить";
		$retVal="";
		$retVal.=drwTableBegin("100%",0);
		$retVal.="<form method=post><input type=hidden name=mod_contacts_action value=$needact><input type=hidden name=id value=$id>";
		$retVal.="<tr><td class=colheader align=center colspan=2>$actionheader</td></tr>";
		$retVal.="<tr><td class=data1 align=right>Название:&nbsp;</td><td class=data1 align=left><input type=text class=text name=contactname size=40 value=\"$contactname\"></td></tr>";
		$retVal.="<tr><td class=data2 align=right valign=top>Данные:&nbsp;</td><td class=data2 align=left><textarea name=contactdata cols=40 rows=4>$contactdata</textarea></td></tr>";
		$retVal.="<tr><td class=data1 align=right>Сортировка:&nbsp;</td><td class=data1 align=left><input type=text class=text name=sort size=10 value=\"$sort\"  onblur=\"if(!checkint(this.value)){alert('недопустимое значение');this.focus()};\"></td></tr>";
		$retVal.="<tr><td class=data2 align=right>Доступность:&nbsp;</td><td class=data2 align=left><input type=checkbox name=visible $visible></td></tr>";
		$retVal.="<tr><td class=data1 align=center colspan=2><input type=submit class=button value=\"$actionbtn\"></td></tr>";
		$retVal.="</form>" . drwTableEnd();
		$retVal.="<br>" . drwTableBegin("100%",0);
		$sql="select `id`, `contactname`, `contactdata`, `sort`, `visible` from `$this->confTable` order by `sort`";
		$this->dbc->sql_query($sql);
		$tdclass="data1";
		while($row=$this->dbc->sql_fetchrow()){
			$visible=($row["visible"]==1)?"доступен":"НЕ доступен";
			$retVal.="<form method=post><input type=hidden name=mod_contacts_action value=edit><input type=hidden name=id value=" . $row["id"] . ">";
			$retVal.="<tr><td class=$tdclass>" . CutQuots($row["contactname"]) . "</td><td class=$tdclass>" . $row["sort"] . "</td><td class=$tdclass align=center>$visible</td><td class=$tdclass align=center><input type=submit class=button value=\"редакт.\"></tr>";
			$retVal.="</form>";
			$tdclass=($tdclass=="data1")?"data2":"data1";
		};
		$retVal.=drwTableEnd();
		return $retVal;
	}
	
	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->confTable`;
			CREATE TABLE `$this->confTable` (
			  `id` int(11) NOT NULL auto_increment,
			  `contactname` varchar(250) NOT NULL default '',
			  `contactdata` text NOT NULL,
			  `sort` int(11) NOT NULL default '0',
			  `visible` int(11) NOT NULL default '0',
			  UNIQUE KEY `id` (`id`)
			)";
		$splitedinstructions=split(";",$installsql);
		foreach($splitedinstructions as $oneinstruction)
			if($reinstall){
				$this->dbc->sql_query($oneinstruction);
			}else if(strstr($oneinstruction,"DROP TABLE")==FALSE){
					$this->dbc->sql_query($oneinstruction);
				};
	}
};
$theAuthModule=new clsContactsModule("contacts","контакты",$db);
$SAmodsArray["contacts"]=$theAuthModule;
$SAmodsArray["contacts"]->prms=MergeConfigs($SAmodsArray["contacts"]->prms,GetConfig(0,"contacts"));
?>