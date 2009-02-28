<?php
class clsCounterModule extends clsStandAloneModule{
	function clsCounterModule($modName,$modDName,$dbconnector){
	 	parent::clsStandAloneModule($modName,$modDName,$dbconnector);
		$this->version="1.0.0";
		$this->helpstring="<p>Модуль статистики. Ведёт учёт посетителей на сайте, а так же отображает статистику (общую и по каждому из разделов по отдельности)</p>";

		$this->prms["ExcludeIPs"]=new ConfigParam("ExcludeIPs");
		$this->prms["ExcludeIPs"]->Description="IP-адреса исключений. Введите через пробел (или перенос строки) IP-адреса (не подсети) для которых не будет использоваться учёт.";
		$this->prms["ExcludeIPs"]->DataType='memo';
		$this->prms["ExcludeIPs"]->Value="127.0.0.1";
		$this->prms["ExcludeIPs"]->Protected=false;
		$this->logTable="mod_" . $this->name . "_log";
	}

	function ClientScript($theFormPrefix, $thePage=1){
		$LocalthePage=$thePage;
	  	$retVal='';
		$ModifedFormPrefix=substr($theFormPrefix,0,strlen($theFormPrefix)-1);
		$ModifedFormPrefix.=" name=\"mod_" . $this->name . "_action_form\">";
		$retVal.=$ModifedFormPrefix . "<input type=hidden name=mod_" . $this->name . "_action value=\"\"><input type=hidden name=id value=0><input type=hidden name=page value=$LocalthePage><input type=hidden name=param1 value=\"\"><input type=hidden name=param2 value=\"\"></form>";
		$retVal.="<script>";
		$retVal.="function mod_" . $this->name . "_action(theAction,thePage,theParam1,theParam2){";
		$retVal.="document.forms['mod_" . $this->name . "_action_form'].mod_" . $this->name . "_action.value=theAction;\n";
		$retVal.="if(thePage)document.forms['mod_" . $this->name . "_action_form'].page.value=thePage;";
		$retVal.="if(theParam1)document.forms['mod_" . $this->name . "_action_form'].param1.value=theParam1;";
		$retVal.="if(theParam2)document.forms['mod_" . $this->name . "_action_form'].param2.value=theParam2;";
		$retVal.="document.forms['mod_" . $this->name . "_action_form'].submit();\n";
		$retVal.="};";
		$retVal.="</script>";
		return $retVal;
	}

	function MakeParamsOuput($theFormPrefix){
		return $this->NodeStat($theFormPrefix,0);
	}

	function NodeStat($theFormPrefix,$theNode){
		global $HTTP_POST_VARS;
		$mod_action=$HTTP_POST_VARS["mod_" . $this->name . "_action"];
		$retVal=drwTableBegin("100%","");
		$startdate=time();
		$enddate=time();
		if(isset($HTTP_POST_VARS["startdatedate"])){
			$startdate=PostToDate("startdate");
			$enddate=PostToDate("enddate");
		};
		$retVal.=$this->ClientScript($theFormPrefix,$pageNum);
		$retVal.="$theFormPrefix<tr><td class=colheader colspan=4>показать отчёт за период (конечная дата не включается в период):</td></tr>";
		$retVal.="<input type=hidden name=mod_" . $this->name . "_action value=showshort>";
		$checked=($HTTP_POST_VARS["verbose"]=="on")?" checked":"";
		$retVal.="<tr><td class=data1 align=center>с:&nbsp;" . DatePickerWOT("startdate",$startdate) . "</td><td class=data1 align=center>" . DatePickerWOT("enddate",$enddate) . "</td><td class=data1 align=center><input type=checkbox name=verbose$checked>&nbsp;-&nbsp;подробно</td><td class=data1 align=center><input type=submit class=button value=\"показать\"></td></tr>";
		$retVal.="</form>" . drwTableEnd();
		if($mod_action=="showshort"){
			$verbose=($HTTP_POST_VARS["verbose"]=="on");
			$retVal.="<br>" . drwTableBegin("100%","");
			$retVal.="<tr><td class=colheader colspan=2>результат выборки</td></tr>";
			$retVal.="<tr><td class=data2 align=center>хосты</td><td class=data2 align=center>хиты</td></tr>";
			$nodefilter=($theNode>0)?" and `node`=$theNode":"";
			$sql="select `remoteip`, count(*) as `hits` from `$this->logTable` where `logtime`>=$startdate and `logtime`<$enddate $nodefilter group by `remoteip`";
			$this->dbc->sql_query($sql);
			$tdclass="data1";
			$totalhosts=0;
			$totalhits=0;
			while($row=$this->dbc->sql_fetchrow()){
				$totalhosts++;
				$totalhits+=$row["hits"];
				if($verbose){
					$retVal.="<tr><td class=$tdclass align=left>" . $row["remoteip"] . "</td><td class=$tdclass align=right>" . $row["hits"] . "</td></tr>";
					$tdclass=($tdclass=="data1")?"data2":"data1";
				};
			};
			$retVal.="<tr><td class=$tdclass align=center style=\"font-weight:bold;\">Всего: $totalhosts</td><td class=$tdclass align=center style=\"font-weight:bold;\">Всего: $totalhits</td></tr>";
			$retVal.=drwTableEnd();
		};
		return $retVal;
	}

	function WriteLog($theNode){
		global $client_ip, $HTTP_SERVER_VARS;
		$excl=preg_split("/\s+/",$this->prms["ExcludeIPs"]->Value);
		$sql="insert into `$this->logTable` (`logtime`, `node`, `remoteip`, `referer`) values (" . time() . ", $theNode, '$client_ip', '" . str_replace("'","''",$HTTP_SERVER_VARS["HTTP_REFERER"]) . "')";
		if(!array_search($client_ip,$excl))$this->dbc->sql_query($sql);
	}

	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->logTable`;
			CREATE TABLE `$this->logTable` (
			`logtime` INT NOT NULL ,
			`node` INT NOT NULL ,
			`remoteip` VARCHAR( 16 ) NOT NULL ,
			`referer` VARCHAR( 250 ) NOT NULL ,
			INDEX ( `logtime` ) 
			);";
		$splitedinstructions=split(";",$installsql);
		foreach($splitedinstructions as $oneinstruction)
			if($reinstall){
				$this->dbc->sql_query($oneinstruction);
			}else if(strstr($oneinstruction,"DROP TABLE")==FALSE){
					$this->dbc->sql_query($oneinstruction);
				};
	}
};

$theCounterModule=new clsCounterModule("counter","счётчик",$db);
$SAmodsArray["counter"]=$theCounterModule;
$SAmodsArray["counter"]->prms=MergeConfigs($SAmodsArray["counter"]->prms,GetConfig(0,"counter"));
?>
