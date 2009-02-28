<?php

class clsRedirModule extends clsModule{


	function clsRedirModule($modName,$modDName,$dbconnector){
	    parent::clsModule($modName,$modDName,$dbconnector);
	    $this->version='1.0.0';
	    $this->helpstring='<p>Модуль для реализации переадресации как и на разделы самого сайта, так и на любые другие URL.</p>';
	    $this->redirTable="mod_redir";
	}

	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
	    global $HTTP_POST_VARS, $SiteTree;
	    $retVal='';
	    if($HTTP_POST_VARS["mod_action"]=="update"){
	    	$newwindow=($HTTP_POST_VARS["newwindow"]=="on")?1:0;
	    	$targettype=$HTTP_POST_VARS["targettype"];
	    	$url=str_replace("'","''",stripslashes($HTTP_POST_VARS["url"]));
	    	$targetnode=$HTTP_POST_VARS["targetnode"];
	    	$sql="update `$this->redirTable` set `newwindow`=$newwindow, `targettype`='$targettype', `url`='$url', `targetnode`=$targetnode where `node`=" . $theNode;
	    	$this->dbc->sql_query($sql);
	    };
	    $sql="select `node`, `newwindow`, `targettype`, `url`, `targetnode` from `$this->redirTable` where `node`=" . $theNode;
	    if(!$this->dbc->sql_query($sql)){
    	        $sqlerror=$this->dbc->sql_error();
		die($sqlerror['message']);
	    };
	    if($this->dbc->sql_numrows()>0){
		$row=$this->dbc->sql_fetchrow();
		$retVal.="<br><center>" . drwTableBegin('600','') . "<tr><td class=colheader colspan=2>свойства перенаправления</td></tr>" . $theFormPrefix . "<input type=hidden name=mod_action value=\"update\">";
		$checked=($row["newwindow"]==1)?" checked":"";
		$selected=($row["targettype"]=="external")?" selected":"";
		$retVal.="<tr><td class=data1 align=right>В новом окне:</td><td class=data1 align=left><input type=checkbox name=newwindow" .$checked . "></td></tr>";
		$retVal.="<tr><td class=data2 align=right>Тип ссылки:</td><td class=data2 align=left><select name=targettype><option value=internal>внутренняя<option value=external$selected>внешняя</select></td></tr>";
		$retVal.="<tr><td class=data1 align=right>Раздел сайта:</td><td class=data1 align=left><select name=targetnode>" . $SiteTree->GetTreeAsOptions(0,$row["targetnode"]) . "</select></td></tr>";
		$retVal.="<tr><td class=data2 align=right>Внешний URL:</td><td class=data2 align=left><input type=text class=text size=60 name=url value=\"" . CutQuots($row['url']) . "\"></td></tr>";
		$retVal.="<tr><td class=data1 align=center colspan=2><input type=submit class=button value=\"обновить\"></td></tr>";
		$retVal.="</form>" . drwTableEnd() . "</center>";
		$retVal.="<br>";
	    };
	    return $retVal;
	}

	function MakeUserOuput($theNode, $theFormPrefix){
	    global $HTTP_SERVER_VARS;
	    $retVal="";
	    $retArray=array();
	    $sql="select `node`, `newwindow`, `targettype`, `url`, `targetnode` from `$this->redirTable` where `node`=" . $theNode;
	    if(!$this->dbc->sql_query($sql)){
	        $sqlerror=$this->dbc->sql_error();
	        die($sqlerror['message']);
	    };
	    if($row=$this->dbc->sql_fetchrow()){
	    	if($row["targettype"]=="external"){
	    		$target=$row["url"];
	    	}else{
	    		$target=GenerateLinkToNode($row["targetnode"]);
	    	};
	    	if($row["newwindow"]==1){
	    		$retVal.="<script>";
	    		$retVal.="self.open('$target','_blank');\n";
	    		$retVal.="history.back();\n";
	    		$retVal.="</script>";
	    	}else{
	    		header("Location: $target");
	    	};
	    };
	    $retArray[0]=$retVal;
	    return $retArray;
	}


	function CreateStructures($theNode){
	    $sql="insert into `$this->redirTable` (`node`, `newwindow`, `targettype`, `url`, `targetnode`) values ($theNode,0,'internal','',0)";
	    $this->dbc->sql_query($sql);
	}

	function DeleteStructures($theNode){
	    $sql="delete from `$this->redirTable` where `node`=$theNode";
	    $this->dbc->sql_query($sql);
	}

	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->redirTable`;
			CREATE TABLE `$this->redirTable` (
			  `node` int(11) NOT NULL default '0',
			  `newwindow` int(11) NOT NULL default '0',
			  `targettype` varchar(30) NOT NULL default '0',
			  `url` varchar(250) default NULL,
			  `targetnode` int(11) NOT NULL default '0'
			);";
		$splitedinstructions=split(";",$installsql);
		foreach($splitedinstructions as $oneinstruction)
			if($reinstall){
				$this->dbc->sql_query($oneinstruction);
			}else if(strstr($oneinstruction,"DROP TABLE")==FALSE){
					$this->dbc->sql_query($oneinstruction);
				};
	}

	function MakeSelfHrefParams($theNode){
		global $contentscript,$hrefSuffix;
		$sql="select `node`, `newwindow`, `targettype`, `url`, `targetnode` from `$this->redirTable` where `node`=" . $theNode;
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror["message"]);
		};
		if($row=$this->dbc->sql_fetchrow()){
			$href="";
			if($row["targettype"]=="external"){
	    			$href=$row["url"];
			}else{
				$href="$contentscript?id=" . $row["targetnode"] . $hrefSuffix;
			};
			$retval="href=\"$href\"";
	    		if($row["newwindow"]==1)
	    			$retval.=" target=_blank";
	    	}else{
			$retval="href=\"$contentscript?id=$theNode$hrefSuffix\" target=_self";
		};
		return $retval;
	}

}

$theRedirModule=new clsRedirModule('redir','перенаправление',$db);
$modsArray['redir']=$theRedirModule;
?>