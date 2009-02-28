<?php
class clsVotesModule extends clsModule{
	function clsVotesModule($modName,$modDName,$dbconnector){
		global $modsArray;
		parent::clsModule($modName,$modDName,$dbconnector);
		$this->version="1.0.0";
		$this->helpstring="<p>Модуль реализует систему голосований на сайте. Один раздел - одно голосование. Если необходимо устроить новое голосование - создаётся новый раздел. Для обсуждения вопроса поставленного на голосование используется модуль \"гостевая книга\"</p>";
		$this->SearchAble=false;

		$this->prms['UseDiscussions']=new ConfigParam('UseDiscussions');
		$this->prms['UseDiscussions']->Description="Использовать модуль \"гостевая книга\" для обсуждения вопроса поставленого на голосование.";
		$this->prms['UseDiscussions']->DataType='bool';
		$this->prms['UseDiscussions']->Value=true;
		$this->prms['UseDiscussions']->Protected=false;

		$this->prms['PageTemplate']=new ConfigParam('PageTemplate');
		$this->prms['PageTemplate']->Description="Общий шаблон вывода страницы с голосованием. Допускаемые для замены значения: question, description, voteform, voteresults, discussion, pagelink";
		$this->prms['PageTemplate']->DataType='memo';
		$this->prms['PageTemplate']->Value="<h4>--question--</h4>--description-- --voteform-- --voteresults-- <h4>обсуждение:</h4>--discussion--";
		$this->prms['PageTemplate']->Protected=false;

		$this->prms['VoteForm']=new ConfigParam('VoteForm');
		$this->prms['VoteForm']->Description="Шаблон вывода формы для голосования. Допускаемые для замены значения: variants, form (&lt;form name=voteform method=..&gt;), pagelink";
		$this->prms['VoteForm']->DataType='memo';
//		$this->prms['VoteForm']->Value="<table border=0 cellpadding=2 cellspacing=1>--form----variants--<tr><td colspan=2 align=center><input type=submit class=button value=\"проголосовать\"></td></tr></form></table>";
		$this->prms['VoteForm']->Value="<table border=0 cellpadding=0 cellspacing=1>--form----variants--<tr><td colspan=2 align=center><a href=\"javascript:document.forms['voteform'].submit()\">проголосовать</a></td></tr></form></table>";
		$this->prms['VoteForm']->Protected=false;

		$this->prms['Variant']=new ConfigParam('Variant');
		$this->prms['Variant']->Description="Шаблон вывода одного варианта для голосования в форме. Допускаемые для замены значения: radio, name";
		$this->prms['Variant']->DataType='memo';
		$this->prms['Variant']->Value="<tr><td align=right>--radio--</td><td align=left>--name--</td></tr>";
		$this->prms['Variant']->Protected=false;

		$this->prms['VariantDevider']=new ConfigParam('VariantDevider');
		$this->prms['VariantDevider']->Description="html-код разделающий варианты ответов";
		$this->prms['VariantDevider']->DataType='char';
		$this->prms['VariantDevider']->Value="";
		$this->prms['VariantDevider']->Protected=false;

		$this->prms['Results']=new ConfigParam('Results');
		$this->prms['Results']->Description="Шаблон вывода результатов голосования. Допускаемые для замены значения: variants, abssummary";
		$this->prms['Results']->DataType='memo';
		$this->prms['Results']->Value="<table border=0 cellpadding=2 cellspacing=1 width=500>--variants--</table>";
		$this->prms['Results']->Protected=false;

		$this->prms['ResultsVariant']=new ConfigParam('ResultsVariant');
		$this->prms['ResultsVariant']->Description="Шаблон вывода результата для одного варианта ответа в шаблоне Results. Допускаемые для замены значения: name, absolute, percent, scaled";
		$this->prms['ResultsVariant']->DataType='memo';
		$this->prms['ResultsVariant']->Value="<tr><td align=left>--name--</td><td align=right>--percent--%</td></tr><tr><td align=left><img src=gformat.gif border=0 width=--scaled-- height=8></td></tr>";
		$this->prms['ResultsVariant']->Protected=false;

		$this->prms['ResultsVariantDevider']=new ConfigParam('ResultsVariantDevider');
		$this->prms['ResultsVariantDevider']->Description="html-код разделающий варианты ответов при выводе результатов голосования";
		$this->prms['ResultsVariantDevider']->DataType='char';
		$this->prms['ResultsVariantDevider']->Value="";
		$this->prms['ResultsVariantDevider']->Protected=false;


		$this->prms['ResultScaleFactor']=new ConfigParam('ResultScaleFactor');
		$this->prms['ResultScaleFactor']->Description="Число до которого масштабировать общую сумму голосов при выводе (для вывода результата с процентах - 100)";
		$this->prms['ResultScaleFactor']->DataType='int';
		$this->prms['ResultScaleFactor']->Value="400";
		$this->prms['ResultScaleFactor']->Protected=false;

		$this->mainTable="mod_votes_general";
		$this->variantsTable="mod_votes_variants";
		$this->resultsTable="mod_votes_results";
		$this->gbModule=$modsArray["guestbook"];
	}

	function ClientScript($theNode, $theFormPrefix, $thePage=1){
		$LocalthePage=$thePage;
		$retVal='';
		$ModifedFormPrefix=substr($theFormPrefix,0,strlen($theFormPrefix)-1);
		$ModifedFormPrefix.=' name="mod_votes_action_form">';
		$retVal.=$ModifedFormPrefix . "<input type=hidden name=mod_votes_action value=\"\"><input type=hidden name=id value=0><input type=hidden name=page value=$LocalthePage><input type=hidden name=param1 value=\"\"></form>";
		$retVal.="<script>";
		$retVal.="function mod_votes_action(theAction,theID,thePage,theParam1){";
		$retVal.="document.forms['mod_votes_action_form'].mod_votes_action.value=theAction;\n";
		$retVal.="document.forms['mod_votes_action_form'].id.value=theID;\n";
		$retVal.="if(thePage)document.forms['mod_votes_action_form'].page.value=thePage;";
		$retVal.="if(theParam1)document.forms['mod_votes_action_form'].param1.value=theParam1;";
		$retVal.="document.forms['mod_votes_action_form'].submit();\n";
		$retVal.="};";
		$retVal.="</script>";
		return $retVal;
	}

	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
		global $modsArray;
		global $HTTP_POST_VARS;
		$retVal=drwTableBegin('100%','') . "<tr><td class=colheader align=center><a href=\"javascript:mod_votes_action('',0)\" class=header>результаты голосования</a></td><td class=colheader align=center><a class=header href=\"javascript:mod_votes_action('params',0)\">параметры голосования</a></td><td class=colheader align=center><a class=header href=\"javascript:mod_votes_action('variants',0)\">варианты ответов</a></td><td class=colheader align=center><a class=header href=\"javascript:mod_votes_action('gb',0)\">обсуждение</a></td></tr>";
		$retVal.=$this->ClientScript($theNode, $theFormPrefix, $PageNum) . drwTableEnd();
		$mod_action=$HTTP_POST_VARS["mod_votes_action"];
		if($mod_action=="gb"){
			$theLocalFormPrefix=substr($theFormPrefix,0,strlen($theFormPrefix)-1);
			$theLocalFormPrefix.=" name=mod_gb_action_form><input type=hidden name=mod_votes_action value=gb><input type=hidden name=poeben value=poeben>";
			$retVal.=$this->gbModule->MakeAdminOuput($theNode, $theLocalFormPrefix, $theSessionSettings);
		};
		if($mod_action=="insert_variant"){
			$sql="insert into `" . $this->variantsTable . "` (`votenode`,`name`,`sort`) values ($theNode,'" . $HTTP_POST_VARS["name"] . "', " . $HTTP_POST_VARS["sort"] . ")";
			$this->dbc->sql_query($sql);
			$insertedresult=$this->dbc->sql_nextid();
			$sql="insert into `" . $this->resultsTable . "` (`votenode`,`variant`,`votedate`,`remoteip`) values ($theNode,'" . $insertedresult . "', 0, '-')";
			$this->dbc->sql_query($sql);
			$mod_action="variants";
		};

		if($mod_action=="delete_variant"){
			$sql="delete from `" . $this->variantsTable . "` where `id`=" . $HTTP_POST_VARS["param1"];
			$this->dbc->sql_query($sql);
			$mod_action="variants";
		};

		if($mod_action=="variants"){
			$retVal.=$this->ShowVoteVariants($theNode, $theFormPrefix, $theSessionSettings);
		};
		if($mod_action=="update_general"){
			$name=$HTTP_POST_VARS["name"];
			$closed=($HTTP_POST_VARS["closed"]=="on")?1:0;
			$showresults=($HTTP_POST_VARS["showresults"]=="on")?1:0;
			$sql="update `" . $this->mainTable . "` set `name`='$name', `closed`=$closed, `closedate`=" . time() . ", `showresults`=$showresults where `node`=$theNode";
			$this->dbc->sql_query($sql);
			$mod_action="params";
		};
		if($mod_action=="params"){
			$retVal.=$this->ShowVoteParams($theNode, $theFormPrefix, $theSessionSettings);
		};
		if($mod_action==""){
			$retVal.=$this->ShowAdminResults($theNode, $theFormPrefix, $theSessionSettings);
		};
		return $retVal;
	}

	function ShowVoteParams($theNode, $theFormPrefix, $theSessionSettings){
		$sql="select `node`, `name`, `description`, `createdate`, `closed`, `closedate`, `showresults` from `" . $this->mainTable . "` where `node`=" . $theNode;
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror["message"]);
		};
		if($this->dbc->sql_numrows()==0)return "";
		$row=$this->dbc->sql_fetchrow();
		$showresults=($row["showresults"]==1)?" checked":"";
		if($row["closed"]==1){
			$closed=" checked readonly";
			$closedate="&nbsp;&nbsp;" . date("d.m.Y H:i",$row['closedate']);
		}else{
			$closed="";
			$closedate="";
		};
		$closereason=($row["closed"]==1)?" checked readonly":"";
		$textID=$row["description"];
		$retVal="";
		$retVal.=drwTableBegin("100%","");
		$retVal.=$theFormPrefix . "<input type=hidden name=mod_votes_action value=update_general>";
		$retVal.="<tr><td class=header colspan=2>Параметры голосования:</td></tr>";
		$retVal.="<tr><td class=data1 align=right>Вопрос:</td><td class=data1 align=left><input type=text class=text size=50 name=name value=\"" . CutQuots($row["name"]) . "\"></td></tr>";
		$retVal.="<tr><td class=data2 align=right>Показывать результаты:</td><td class=data2 align=left><input type=checkbox name=showresults $showresults></td></tr>";
		$retVal.="<tr><td class=data1 align=right>Закрыть голосование:</td><td class=data1 align=left><input type=checkbox name=closed $closed>$closedate</td></tr>";
		$retVal.="<tr><td class=data2 align=center colspan=2>Описание (пояснение вопроса):</td></tr>";
		$retVal.="<tr><td class=data2 align=center colspan=2><iframe name=\"mod_vote_description_editor\" border=0 width=100% height=400 marginwidth=0 marginheight=0 scrolling=yes frameborder=0></iframe></td></tr>";
		$retVal.="<tr><td class=data1 align=center colspan=2><input type=submit class=button value=\"обновить\"></td></tr>";
		$retVal.="</form>" . drwTableEnd();
		$retVal.="<form method=post action=post.php enctype=\"multipart/form-data\" name=mod_vote_description_editor_go_form target=mod_vote_description_editor><input type=hidden name=textID value=$textID></form>";
		$retVal.="<script>document.forms['mod_vote_description_editor_go_form'].submit();</script>";
		return $retVal;
	}

	function ShowVoteVariants($theNode, $theFormPrefix, $theSessionSettings){
		$retVal="";
		$sql="select `id`, `votenode`, `name`, `sort` from `" . $this->variantsTable . "` where `votenode`=$theNode order by `sort`";
		$this->dbc->sql_query($sql);
		$retVal.=drwTableBegin("100%","");
		$retVal.="<tr><td colspan=3 class=colheader>существующие варианты ответов</td></tr>";
		$tdclass="data1";
		while($row=$this->dbc->sql_fetchrow()){
			$retVal.="<tr><td class=$tdclass>" . CutQuots($row["name"]) . "</td><td class=$tdclass>" . $row["sort"] . "</td><td class=$tdclass align=center><a href=\"javascript:mod_votes_action('delete_variant',0,0," . $row["id"] . ")\">удалить</a></td></tr>";
			$tdclass=($tdclass=="data1")?"data2":"data1";
		};
		$retVal.=drwTableEnd() . "<br>";

		$retVal.=drwTableBegin("100%","");
		$retVal.=$theFormPrefix . "<input type=hidden name=mod_votes_action value=insert_variant>";
		$retVal.="<tr><td colspan=2 class=colheader>добавить вариант ответа</td></tr>";
		$retVal.="<tr><td class=data1 align=right>Наименование:</td><td class=data1 align=left><input type=text class=text name=name size=40></td></tr>";
		$retVal.="<tr><td class=data2 align=right>Сортировка:</td><td class=data2 align=left><input type=text class=text name=sort size=10 value=10></td></tr>";
		$retVal.="<tr><td class=data1 align=center colspan=2><input type=submit class=button value=\"добавить\"></td></tr>";
		$retVal.="</form>" . drwTableEnd();
		return $retVal;
	}

	function ShowAdminResults($theNode, $theFormPrefix, $theSessionSettings){
		$retVal="";
		$retVal.=drwTableBegin("100%","");
		$retVal.="<tr><td class=colheader colspan=2>Результаты голосования</td></tr>";
		$sql="select v.`name` as `name`, v.`sort` as `sort`, count(*) as `count` from `" . $this->variantsTable . "` as v right join `" . $this->resultsTable . "` as r on r.`variant`=v.`id` where v.`votenode`=$theNode group by v.`name` order by v.`sort`";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror["message"]);
		};
		$tdclass="data1";
		while($row=$this->dbc->sql_fetchrow()){
			$retVal.="<td class=$tdclass>" . CutQuots($row["name"]) . "</td><td class=$tdclass>" . ($row["count"]-1) . "</td></tr>";
			$tdclass=($tdclass=="data1")?"data2":"data1";
		};
		$retVal.=drwTableEnd();
		return $retVal;
	}

	function MakeUserOuput($theNode, $theFormPrefix, $alwaysshowform=false){
		global $HTTP_POST_VARS, $HTTP_GET_VARS, $client_ip;
		$sql="select * from `" . $this->resultsTable . "` where `remoteip`='$client_ip' and `votenode`=$theNode";
		$this->dbc->sql_query($sql);
		$alreadyvoted=($this->dbc->sql_numrows()>0);
		if($HTTP_POST_VARS["mod_votes_action"]=="vote"){
			$variantid=($HTTP_POST_VARS["variant"]>0)?$HTTP_POST_VARS["variant"]:0;
			if((!$alreadyvoted)&&($variantid!=0)){
				$sql="insert into `" . $this->resultsTable . "` (`votenode`, `variant`, `votedate`, `remoteip`) values ($theNode, $variantid , " . time() . ", '$client_ip')";
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror["message"]);
				};
			};
			header("Location: $theFormPrefix&voted");
		};
		$retArray=array();
		$retVal=$this->prms['PageTemplate']->Value;
		$sql="select `node`, `name`, `description`, `createdate`, `closed`, `closedate`, `showresults` from `" . $this->mainTable . "` where `node`=$theNode";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror["message"]);
		};
		if($this->dbc->sql_numrows()==0)return $retArray;
		$row=$this->dbc->sql_fetchrow();
		$voteform=($row["closed"]==0)?$this->ShowVoteForm($theNode, $theFormPrefix):"";
		$voteform=($alreadyvoted && (!$alwaysshowform))?"":$voteform;
		$voteresults=($row["showresults"]==1)?$this->ShowVoteResults($theNode, $theFormPrefix):"";
		if($this->prms["UseDiscussions"]->Value){
			$tmpArr=$this->gbModule->MakeUserOuput($theNode, $theFormPrefix);
			$discussion=$tmpArr[0];
		}else{
			$discussion="";
		};
		$retVal=str_replace("--question--",CutQuots($row["name"]),$retVal);
		$retVal=str_replace("--description--",html_display($row["description"]),$retVal);
		$retVal=str_replace("--voteform--",$voteform,$retVal);
		$retVal=str_replace("--voteresults--",$voteresults,$retVal);
		$retVal=str_replace("--discussion--",$discussion,$retVal);
		$retVal=str_replace("--pagelink--",$theFormPrefix,$retVal);
		$retArray[0]=$retVal;
		return $retArray;
	}	

	function ShowVoteForm($theNode, $theFormPrefix){
		$retVal=$this->prms["VoteForm"]->Value;
		$sql="select `id`, `name`, `sort` from `" . $this->variantsTable . "` where `votenode`=$theNode order by `sort`";
		$this->dbc->sql_query($sql);
		$allVariants="";
		while($row=$this->dbc->sql_fetchrow()){
			$radio="<input type=radio name=variant value=\"" . $row["id"] . "\">";
			$name=CutQuots($row["name"]);
			$oneRow=$this->prms["Variant"]->Value;
			$oneRow=str_replace("--radio--",$radio,$oneRow);
			$oneRow=str_replace("--name--",$name,$oneRow);
			$allVariants.=$oneRow . $this->prms["VariantDevider"]->Value;
		};
		if(strlen($allVariants)>strlen($this->prms["VariantDevider"]->Value))$allVariants=substr($allVariants,0,(strlen($allVariants)-strlen($this->prms["VariantDevider"]->Value)));
		$retVal=str_replace("--variants--",$allVariants,$retVal);
		$retVal=str_replace("--form--","<form name=voteform method=post action=\"$theFormPrefix\"><input type=hidden name=mod_votes_action value=vote>",$retVal);
		$retVal=str_replace("--pagelink--",$theFormPrefix,$retVal);
		return $retVal;
	}

	function ShowVoteResults($theNode, $theFormPrefix){
		$retVal=$this->prms["Results"]->Value;
		$sql="select v.`id` as `id`, v.`name` as `name`, v.`sort` as `sort`, count(*) as `count` from `" . $this->variantsTable . "` as v inner join `" . $this->resultsTable . "` as r on r.`variant`=v.`id` where v.`votenode`=$theNode group by v.`name` order by v.`sort`";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror["message"]);
		};
		$allresults=array();
		$sumvotes=0;
		while($row=$this->dbc->sql_fetchrow()){
			$allresults["res" . $row["id"]]=$row;
			$sumvotes+=$row["count"]-1;
		};
		$sumvotes=($sumvotes>0)?$sumvotes:1;
		$allVariants="";
		foreach($allresults as $oneres){
			$scaled=round(($oneres["count"]-1)*$this->prms["ResultScaleFactor"]->Value/$sumvotes);
			$percent=round(($oneres["count"]-1)*100/$sumvotes);
			$name=CutQuots($oneres["name"]);
			$oneVariant=$this->prms["ResultsVariant"]->Value;
			$oneVariant=str_replace("--name--",$name,$oneVariant);
			$oneVariant=str_replace("--absolute--",($oneres["count"]-1),$oneVariant);
			$oneVariant=str_replace("--scaled--",$scaled,$oneVariant);
			$oneVariant=str_replace("--percent--",$percent,$oneVariant);
			$allVariants.=$oneVariant . $this->prms["ResultsVariantDevider"]->Value;
		};
		if(strlen($allVariants)>strlen($this->prms["ResultsVariantDevider"]->Value))$allVariants=substr($allVariants,0,(strlen($allVariants)-strlen($this->prms["ResultsVariantDevider"]->Value)));
		$retVal=str_replace("--variants--",$allVariants,$retVal);
		$retVal=str_replace("--abssummary--",$sumvotes,$retVal);
		return $retVal;
	}

	function CreateStructures($theNode){
		$descText=text_create_new();
		$sql="insert into `" . $this->mainTable . "` (`node`,`name`,`description`,`createdate`,`closed`,`closedate`,`showresults`) values ($theNode,'',$descText," . time() . ",0,0,0)";
		$this->dbc->sql_query($sql);
		$this->gbModule->CreateStructures($theNode);
	}
	
	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->mainTable`;
			CREATE TABLE `$this->mainTable` (
			  `node` int(11) NOT NULL default '0',
			  `name` varchar(250) NOT NULL default '',
			  `description` int(11) NOT NULL default '0',
			  `createdate` int(11) NOT NULL default '0',
			  `closed` int(11) NOT NULL default '0',
			  `closedate` int(11) NOT NULL default '0',
			  `showresults` int(11) NOT NULL default '0'
			);
			DROP TABLE IF EXISTS `$this->resultsTable`;
			CREATE TABLE `$this->resultsTable` (
			  `votenode` int(11) NOT NULL default '0',
			  `variant` int(11) NOT NULL default '0',
			  `votedate` int(11) NOT NULL default '0',
			  `remoteip` varchar(20) NOT NULL default ''
			);
			DROP TABLE IF EXISTS `$this->variantsTable`;
			CREATE TABLE `$this->variantsTable` (
			  `id` int(11) NOT NULL auto_increment,
			  `votenode` int(11) NOT NULL default '0',
			  `name` varchar(250) NOT NULL default '',
			  `sort` int(11) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
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
$theVotesModule=new clsVotesModule("votes","голосование",$db);
$modsArray["votes"]=$theVotesModule;

