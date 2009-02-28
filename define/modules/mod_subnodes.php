<?php

class clsSubnodesModule extends clsModule{
	function clsSubnodesModule($modName,$modDName,$dbconnector){
		global $SiteTree;
	 	parent::clsModule($modName,$modDName,$dbconnector);
	 	$this->SearchAble=false;
		$this->version="1.0.5";
		$this->helpstring='<p>ћодуль предназначен дл€ вывода списка очерних разделов по шаблону определЄнному параметрами.</p>';

		$this->prms['PageTemplate']=new ConfigParam('PageTemplate');
		$this->prms['PageTemplate']->Description="Ўаблон вывода списка разделов. ƒопускаемые дл€ замены значени€ - itemslist.";
		$this->prms['PageTemplate']->DataType='memo';
		$this->prms['PageTemplate']->Value="<ul>--itemslist--</ul>";

		$this->prms['ItemTemplate']=new ConfigParam('ItemTemplate');
		$this->prms['ItemTemplate']->Description="Ўаблон отображени€ одной ссылки на дочернй раздел внутри шаблона PageTemplate. ƒопускаемые дл€ замены значени€: name, linkparams, announce, nodecontent";
		$this->prms['ItemTemplate']->DataType='memo';
		$this->prms['ItemTemplate']->Value='<li><a --linkparams-->--name--</a></li>';
		$this->prms['ItemTemplate']->Protected=false;

		$this->prms['ItemsDevider']=new ConfigParam('ItemsDevider');
		$this->prms['ItemsDevider']->Description='html-код раздел€ющий отдельные ссылки на странице';
		$this->prms['ItemsDevider']->DataType='memo';
		$this->prms['ItemsDevider']->Value="";
		$this->prms['ItemsDevider']->Protected=false;

		$this->modTable="mod_subnodes";

		$this->siteTree=$SiteTree;
	}

	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
		$retVal="";
		$retVal.="<script>function mod_subnodes_edittext(theTextID){" .
			"	self.showModalDialog(\"post-dialog.php?text=\"+theTextID,\"\",\"center:yes;edge:rized;resizable:no;scroll:no;help:no;status:no;unadorned:yes;dialogWidth:720px;dialogHeight:600px\");\n" .
			"};</script>";
		$retVal.=drwTableBegin("100%",0);
		$subNodes=$this->siteTree->GetExpandedLevel($theNode,"");
		$sql="select `child`,`rtext` from `$this->modTable` where `node`=$theNode";
		$this->dbc->sql_query($sql);
		$allsnodes=array();
		while($row=$this->dbc->sql_fetchrow()){
			$allsnodes["child" . $row["child"]]=$row["rtext"];
		};
		if(isset($allsnodes["child" . $theNode])){
			$retVal.="<tr><td class=colheader align=left>¬ступительный текст раздела</td><td class=colheader align=center><input type=button class=button value=\"редактировать\" onclick=\"mod_subnodes_edittext(" . $allsnodes["child" . $theNode] . ")\"></td></tr>";
		}
		$tdclass="data1";
		foreach($subNodes as $subNode){
			if(!isset($allsnodes["child" . $subNode[$this->siteTree->cKeyField]])){
				$createdtext=text_create_new();
				$sql="insert into `$this->modTable` (`node`, `child`, `rtext`) values ($theNode, " . $subNode[$this->siteTree->cKeyField] . ", $createdtext)";
				$this->dbc->sql_query($sql);
				$allsnodes["child" . $subNode[$this->siteTree->cKeyField]]=$createdtext;
			};
			$retVal.="<tr><td class=$tdclass align=left>" . CutQuots($subNode[$this->siteTree->cNameField]) . "</td><td class=$tdclass align=center><input type=button class=button value=\"анонс\" onclick=\"mod_subnodes_edittext(" . $allsnodes["child" . $subNode[$this->siteTree->cKeyField]] . ")\"></td></tr>";
			$tdclass=($tdclass=="data1")?"data2":"data1";
		};
		$retVal.=drwTableEnd();
		return $retVal;
	}

	function MakeUserOuput($theNode, $theFormPrefix){
		global $contentscript,$modsArray;
		$retVal=array();
		$clientslisting='';
		$sql="select `t1`.`child`, `t1`.`rtext`, `texts`.`text` as `text` from `$this->modTable` as `t1` inner join `texts` on `texts`.`id`=`t1`.`rtext` where `t1`.`node`=$theNode";
		$this->dbc->sql_query($sql);
		$allsnodes=array();
		while($row=$this->dbc->sql_fetchrow()){
			$allsnodes["child" . $row["child"]]=$row["text"];
		};
		$prefixtext=(isset($allsnodes["child" . $theNode]))?$allsnodes["child" . $theNode]:"";
		$subNodes=$this->siteTree->GetExpandedLevel($theNode,"visible=1");
		foreach($subNodes as $anode){
			if($clientslisting!='')$clientslisting.=$this->prms['ItemsDevider']->Value;
			if(($anode["type"]!="subnodes")){
				$nodecontent=$modsArray[$anode["type"]]->MakeUserOuput($anode["id"],$theFormPrefix);
				$nodecontent=$nodecontent[0];
			}else{
				$nodecontent="";
			};
			$announce=(isset($allsnodes["child" . $anode[$this->siteTree->cKeyField]]))?$allsnodes["child" . $anode[$this->siteTree->cKeyField]]:"";
			$oneLine=$this->prms['ItemTemplate']->Value;
			$oneLine=str_replace("--linkparams--", $modsArray[$anode['type']]->MakeSelfHrefParams($anode[$this->siteTree->cKeyField]),$oneLine);
			$oneLine=str_replace("--name--",CutQuots($anode["name"]),$oneLine);
			$oneLine=str_replace("--nodecontent--",$nodecontent,$oneLine);
			$oneLine=str_replace("--announce--",$announce,$oneLine);
			$clientslisting.=$oneLine;
		}
		$retVal[0]=str_replace("--itemslist--",$clientslisting,$this->prms['PageTemplate']->Value);
		$retVal[0]=str_replace("--prefixtext--",$prefixtext,$retVal[0]);
		return $retVal;
	}

	function CreateStructures($theNode){
		$sql="select `node` from `$this->modTable` where `node`=$theNode";
		$this->dbc->sql_query($sql);
		if($this->dbc->sql_numrows==0){
			$createdtext=text_create_new();
			$sql="insert into `$this->modTable` (`node`, `child`, `rtext`) values ($theNode, $theNode, $createdtext)";
			$this->dbc->sql_query($sql);
		};
	}

	function DeleteStructures($theNode){
		$sql="delete from `$this->modTable` where `node`=$theNode";
		$this->dbc->sql_query($sql);
	}

	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->modTable`;
			CREATE TABLE `$this->modTable` (
			  `node` int(11) NOT NULL default '0',
			  `child` int(11) NOT NULL default '0',
			  `rtext` int(11) NOT NULL default '0'
			)";
		$splitedinstructions=split(";",$installsql);
		foreach($splitedinstructions as $oneinstruction)
			if($reinstall){
				$this->dbc->sql_query($oneinstruction);
			}else if(strstr($oneinstruction,"DROP TABLE")==FALSE){
					$this->dbc->sql_query($oneinstruction);
				};
	}
}

$theSubnodesModule=new clsSubnodesModule('subnodes','листинг',$db);
$modsArray['subnodes']=$theSubnodesModule;
?>