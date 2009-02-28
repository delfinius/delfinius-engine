<?php

class clsSubscribeModule extends clsStandAloneModule{
	function clsSubscribeModule($modName,$modDName,$dbconnector){
	 	parent::clsStandAloneModule($modName,$modDName,$dbconnector);
		$this->version="1.0.1";
		$this->helpstring='<p>Модуль предназначен для произведения рассылок.</p>';
		$this->prms["from"]=new ConfigParam("from");
		$this->prms["from"]->Description="Имя пользователя от которого будет производиться рассылка.";
		$this->prms["from"]->DataType="char";
		$this->prms["from"]->Value="Dyomin Alexey";
		$this->prms["from"]->Protected=false;

		$this->prms["mailfrom"]=new ConfigParam("mailfrom");
		$this->prms["mailfrom"]->Description="E-mail адрес от имени кого будет производиться рассылка.";
		$this->prms["mailfrom"]->DataType="char";
		$this->prms["mailfrom"]->Value="delfin@extrim.ru";
		$this->prms["mailfrom"]->Protected=false;

		$this->prms["form"]=new ConfigParam("form");
		$this->prms["form"]->Description="Форма подписки на новости. Допускаемые для замены значения: form ( <form method....> )";
		$this->prms["form"]->DataType="memo";
		$this->prms["form"]->Value="<table width=175 border=0 cellpadding=0 cellspacing=0><tr><td bgcolor=#ECEDED><table cellpadding=1 cellspacing=1 width=175>--form--<tr><td align=right style=\"color:#609EE3;font-weight:bold;\">ваш e-mail:</td><td align=center><input type=text class=text name=email maxlength=100 size=15></td></tr><tr><td>&nbsp;</td><td align=center><a style=\"color:#9D0000\" href=\"javascript:document.forms['subscribeform'].submit()\">подписаться</a></td></tr></form></table></td></tr></table>";
		$this->prms["form"]->Protected=false;

		$this->prms["messagetemplate"]=new ConfigParam("messagetemplate");
		$this->prms["messagetemplate"]->Description="Шаблон для формирования текста сообщения. Допускаемые для замены значения: text, unsubscribelink";
		$this->prms["messagetemplate"]->DataType="memo";
		$this->prms["messagetemplate"]->Value="Новости сайта msb-ural.ru\r\n--text--\r\n\r\nЕсли вы желаете отписаться от рассылки перейдите по следующей ссылке: --unsubscribelink--";
		$this->prms["messagetemplate"]->Protected=false;
		$this->sendsTable="mod_subscribe_sends";
		$this->usersTable="mod_subscribe_users";

	}


	function MakeAdminOuput($theNode,$theFormPrefix,$sendKey,$sendDesc,$sendSubj,$sendText,$formParams){
		global $HTTP_POST_VARS, $HTTP_GET_VARS, $SessionSettings, $SiteMainURL, $contentscript;
		$retVal="";
		$this->prms=MergeConfigs($this->prms,GetConfig(0,"subscribe"));
		if($HTTP_POST_VARS["mod_subscribe_action"]=="sendletter"){
			$messageTS=$HTTP_POST_VARS["message"];
			$subjectTS=$HTTP_POST_VARS["subject"];
			$fromTS=$HTTP_POST_VARS["from"];
			$mailfromTS=$HTTP_POST_VARS["mailfrom"];
			$recipientsTS=$HTTP_POST_VARS["besent"];
			$recipientsTSArray=split("(\r)?\n",$recipientsTS);
			$sql="update `$this->sendsTable` set `subject`='" . str_replace("'", "''", stripslashes($subjectTS)) . "', `text`='" . str_replace("'", "''", stripslashes($messageTS)) . "' where `sendkey`='$sendKey'";
			if(!$this->dbc->sql_query($sql)){
			    $sqlerror=$this->dbc->sql_error();
			    die($sqlerror['message']);
			};
			$subjectTS=stripslashes($subjectTS);
			$messageTS=stripslashes($messageTS);
			foreach($recipientsTSArray as $recipientTS){
				$mailheader="MIME-Version: 1.0\nContent-type: text/plain; charset=windows-1251\nDate: " . gmdate('D, d M Y H:i:s', time()) . " UT\nX-Priority: 3\nX-MSMail-Priority: Normal\nX-Mailer: PHP\n";
				$mailheader.="From: " . stripslashes($fromTS) . "<$mailfromTS>\n";
				$finalmessageTS=str_replace("--recipient--",$recipientTS,$messageTS);
				if(mail($recipientTS,$subjectTS,$finalmessageTS,$mailheader)){
					$sql="update `$this->sendsTable` set `recipients`=concat(`recipients`,';$recipientTS') where `sendkey`='$sendKey'";
					if(!$this->dbc->sql_query($sql)){
					    $sqlerror=$this->dbc->sql_error();
					    die($sqlerror['message']);
					};
				};
			};
		};
		$sql="select `id` from `$this->sendsTable` where `sendkey`='$sendKey'";
		if(!$this->dbc->sql_query($sql)){
		    $sqlerror=$this->dbc->sql_error();
		    die($sqlerror['message']);
		};
		if($this->dbc->sql_numrows()==0){
			$afullmessage=$this->prms["messagetemplate"]->Value;
			$unsubscribelink="http://$SiteMainURL$contentscript?id=$theNode&unsubscribe=--recipient--";
			$afullmessage=str_replace("--text--",$sendText,$afullmessage);
			$afullmessage=str_replace("--unsubscribelink--",$unsubscribelink,$afullmessage);
			$sql="insert into `$this->sendsTable` (`sentdate`, `sender`, `sendkey`, `recipients`, `subject`, `text`, `description`) values(" . time() . ", '" . $SessionSettings["login"] . "', '$sendKey' ,'', '" . str_replace("'", "''", $sendSubj) . "', '" . str_replace("'", "''", $afullmessage) . "', '" . str_replace("'", "''", $sendDesc) . "')";
			if(!$this->dbc->sql_query($sql)){
			    $sqlerror=$this->dbc->sql_error();
			    die($sqlerror['message']);
			};
		};
		$sql="select `id`, `sentdate`, `sender`, `sendkey`, `recipients`, `subject`, `text`, `description` from `$this->sendsTable` where `sendkey`='$sendKey'";
		if(!$this->dbc->sql_query($sql)){
		    $sqlerror=$this->dbc->sql_error();
		    die($sqlerror['message']);
		};
		$row=$this->dbc->sql_fetchrow();
		$alreadySent=$row["recipients"];
		$alreadySentArray=array();
		$alreadySentTmpArray=split(";",$alreadySent);
		$alreadySent=str_replace(";","\r\n",$alreadySent);
		foreach($alreadySentTmpArray as $email)$alreadySentArray[$email]=1;
		$sql="select `email` from `$this->usersTable` where `active`=1 order by `email`";
		if(!$this->dbc->sql_query($sql)){
		    $sqlerror=$this->dbc->sql_error();
		    die($sqlerror['message']);
		};
		$needToSend="";
		while($emailrow=$this->dbc->sql_fetchrow()){
			if($alreadySentArray[$emailrow['email']]!=1)$needToSend.=$emailrow['email'] . "\r\n";
		};
		$retVal.=drwTableBegin('100%','') . "<tr><td class=colheader colspan=2>Разослать сообщение</td></tr>" . $theFormPrefix . $formParams . "<input type=hidden name=mod_subscribe_action value=\"sendletter\">";
		$retVal.="<tr><td class=data1 align=right>Отправитель:</td><td class=data1 align=left><input type=text class=text name=from value=\"" . CutQuots($this->prms["from"]->Value) . "\"></td></tr>";
		$retVal.="<tr><td class=data2 align=right>E-mail отправителя:</td><td class=data2 align=left><input type=text class=text name=mailfrom value=\"" . CutQuots($this->prms["mailfrom"]->Value) . "\"></td></tr>";
		$retVal.="<tr><td class=data1 align=right>Тема сообщения:</td><td class=data1 align=left><input type=text class=text name=subject value=\"" . CutQuots($row["subject"]) . "\"></td></tr>";
		$retVal.="<tr><td class=data2 align=right valign=top>Сообщение:</td><td class=data2 align=left><textarea name=message cols=74 rows=10>" . CutQuots($row["text"]) . "</textarea></td></tr>";
		$retVal.="<tr><td colspan=2 class=data1><table width=100% border=0 cellpadding=0 cellspacing=0><tr><td align=center>Отправлено:</td><td align=center>Ожидает отправки</td></tr>";
		$retVal.="<tr><td align=center><textarea name=sent cols=40 rows=20 readonly>" .$alreadySent . "</textarea></td><td align=center><textarea name=besent cols=40 rows=20>" . $needToSend . "</textarea></td></tr>";
		$retVal.="</table></td></tr>";
		$retVal.="<tr><td colspan=2 class=data1 align=center><input type=submit class=button value=\"разослать\"></td></tr></form>";
		$retVal.=drwTableEnd();
		return $retVal;
	}

	function MakeUserOuput($theNode, $theFormPrefix){
		global $HTTP_POST_VARS, $HTTP_GET_VARS, $client_ip;
		$this->prms=MergeConfigs($this->prms,GetConfig(0,"subscribe"));
		$retVal="";
		if($HTTP_POST_VARS["subscribe_action"]=="subscribe"){
			$email=$HTTP_POST_VARS["email"];
			$sql="select `email` from `$this->usersTable` where `email`='$email'";
			$this->dbc->sql_query($sql);
			if($this->dbc->sql_numrows()>0){
				$sql="update `$this->usersTable` set `active`=1 where `email`='$email'";
			}else{
				$sql="insert into `$this->usersTable` (`email`,`regdate`,`active`,`node`,`regip`) values ('$email'," . time() . ",1,$theNode, '$client_ip')";
			};
			$this->dbc->sql_query($sql);
			$retVal="Ваш e-mail добавлен в список рассылки.";
		};
		if(isset($HTTP_GET_VARS["unsubscribe"])){
			$email=$HTTP_GET_VARS["unsubscribe"];
			$sql="update `$this->usersTable` set `active`=0 where `email`='$email'";
			$this->dbc->sql_query($sql);
			$retVal="Ваш e-mail удалён из списка рассылки.";
		};
		if(strlen($retVal)>0){
			$retVal.="<br><a href=\"$theFormPrefix\">вернуться</a>";
		};
		return $retVal;
	}

	function SubscribeForm($theNode, $theFormPrefix){
		$retVal=$this->prms["form"]->Value;
		$retVal=str_replace("--form--","<form method=post action=\"$theFormPrefix\" name=subscribeform><input type=hidden name=subscribe_action value=subscribe>",$retVal);
		return $retVal;
	}

	function MakeParamsOuput($theFormPrefix){
		global $HTTP_POST_VARS;
		if($HTTP_POST_VARS["mod_subscribe_action"]=="delete"){
			$sql="update `$this->usersTable` set `active`=0 where `id`=" . $HTTP_POST_VARS["id"];
			$this->dbc->sql_query($sql);
		};
		$retVal=drwTableBegin("100%","") . "<tr><td class=colheader align=center>подписчик</td><td class=colheader align=center>IP-адрес регистрации</td><td class=colheader align=center>дата регистрации</td><td class=colheader align=center>&nbsp;</td></tr>";
		$sql="select `id`, `email`, `regdate`, `node`, `regip` from `$this->usersTable` where `active`=1 order by `regdate`";
		if(!$this->dbc->sql_query($sql)){
		    $sqlerror=$this->dbc->sql_error();
		    die($sqlerror['message']);
		};
		$tdclass="data2";
		while($row=$this->dbc->sql_fetchrow()){
			$retVal.="$theFormPrefix<input type=hidden name=mod_subscribe_action value=delete><input type=hidden name=id value=" . $row["id"] . "><tr>";
			$retVal.="<td class=$tdclass>" . CutQuots($row["email"]) . "</td>";
			$retVal.="<td class=$tdclass>" . CutQuots($row["regip"]) . "</td>";
			$retVal.="<td class=$tdclass>" . date("d.m.Y",$row["regdate"]) . "</td>";
			$retVal.="<td class=$tdclass align=center><input type=submit class=button value=\"отписать\"></td>";
			$retVal.="</tr></form>";
			$tdclass=($tdclass=="data1")?"data2":"data1";
		};
		$retVal.=drwTableEnd();
		return $retVal;
	}

	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->sendsTable`;
			CREATE TABLE `$this->sendsTable` (
			  `id` int(11) NOT NULL auto_increment,
			  `sentdate` int(11) NOT NULL default '0',
			  `sender` varchar(250) NOT NULL default '',
			  `sendkey` varchar(250) NOT NULL default '',
			  `recipients` text NOT NULL,
			  `text` text NOT NULL,
			  `description` varchar(250) NOT NULL default '',
			  `subject` varchar(250) NOT NULL default '',
			  PRIMARY KEY  (`id`)
			);
			DROP TABLE IF EXISTS `$this->usersTable`;
			CREATE TABLE `$this->usersTable` (
			  `id` int(11) NOT NULL auto_increment,
			  `email` varchar(250) NOT NULL default '',
			  `regdate` int(11) NOT NULL default '0',
			  `regip` varchar(20) NOT NULL default '',
			  `active` int(11) NOT NULL default '0',
			  `node` int(11) NOT NULL default '0',
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
	
}

$theSubscribeModule=new clsSubscribeModule("subscribe","рассылки",$db);
$SAmodsArray["subscribe"]=$theSubscribeModule;
?>