<?php
class clsGuestbookModule extends clsModule{
	function clsGuestbookModule($modName,$modDName,$dbconnector){
		global $SiteMainURL;
		parent::clsModule($modName,$modDName,$dbconnector);
		$this->version="1.3.1";
		$this->helpstring="<p>Модуль реализует гостевую книгу. Пользователи могут оставлять записи, администраторы могу их комментировать или удалять.</p>";
		$this->SearchAble=true;

		$this->prms['PageSize']=new ConfigParam('PageSize');
		$this->prms['PageSize']->Description="Количество отображаемых записей на одной странице.";
		$this->prms['PageSize']->DataType='int';
		$this->prms['PageSize']->Value=20;
		$this->prms['PageSize']->Protected=false;

		$this->prms['AdminPageSize']=new ConfigParam('AdminPageSize');
		$this->prms['AdminPageSize']->Description="Количество отображаемых записей на странице модератора.";
		$this->prms['AdminPageSize']->DataType='int';
		$this->prms['AdminPageSize']->Value=50;
		$this->prms['AdminPageSize']->Protected=true;

		$this->prms['PageTemplate']=new ConfigParam('PageTemplate');
		$this->prms['PageTemplate']->Description="Шаблон отображения страницы с записями. Допускаемое для замены значение: itemslist, pager, messageform";
		$this->prms['PageTemplate']->DataType='memo';
		$this->prms['PageTemplate']->Value="<table cellpadding=0 cellspacing=0 border=0><tr><td width=20><img src=format.gif border=0 width=20 height=1></td><td><table border=0 cellpadding=2 cellspacing=0>--itemslist--</table>--pager--<br><br>--messageform--</td></tr></table>";
		$this->prms['PageTemplate']->Protected=false;

    		$this->prms['plTemplate']=new ConfigParam('plTemplate');
		$this->prms['plTemplate']->Description="Шаблон отображения списка страниц. Заменяет pager в шаблоне PageTemplate. Допускаемое для замены значение: pageslist";
		$this->prms['plTemplate']->DataType='memo';
		$this->prms['plTemplate']->Value="<tr><td><img src=gformat.gif border=0 width=300 height=1><br>страница:&nbsp;--pageslist--</td></tr>";
		$this->prms['plTemplate']->Protected=false;

		$this->prms['ItemTemplate']=new ConfigParam('ItemTemplate');
		$this->prms['ItemTemplate']->Description="Шаблон отображения одной записи внутри шаблона PageTemplate. Допускаемые для замены значения: date, text, author, response";
		$this->prms['ItemTemplate']->DataType='memo';
		$this->prms['ItemTemplate']->Value="<tr><td align=center width=80 style=\"font-size:11px;color:#AFAFAF\">&nbsp;--date--&nbsp;</td><td align=left style=\"color:#2D4CA7;font-weight:bold;\">--author--</td></tr><tr><td width=80>&nbsp;</td><td>--text--</td></tr>--response--";
		$this->prms['ItemTemplate']->Protected=false;

		$this->prms['ResponseTemplate']=new ConfigParam('ResponseTemplate');
		$this->prms['ResponseTemplate']->Description="Шаблон отображения комментария внутри шаблона ItemTemplate. Допускаемые для замены значения: text";
		$this->prms['ResponseTemplate']->DataType='memo';
		$this->prms['ResponseTemplate']->Value="<tr><td>&nbsp;</td><td style=\"color:#2D4CA7\"><span style=\"font-weight:bold;\">комментарий: </span>--text--</td>";
		$this->prms['ResponseTemplate']->Protected=false;

		$this->prms['ItemsDevider']=new ConfigParam('ItemsDevider');
		$this->prms['ItemsDevider']->Description="html-код разделяющий отдельные записи на странице";
		$this->prms['ItemsDevider']->DataType='memo';
		$this->prms['ItemsDevider']->Value="<tr><td colspan=2><img src=format.gif border=0 width=30 height=1><img src=gformat.gif border=0 width=400 height=1></td></tr>";
		$this->prms['ItemsDevider']->Protected=false;

		$this->prms['plInactiveTemplate']=new ConfigParam('plInactiveTemplate');
		$this->prms['plInactiveTemplate']->Description="Шаблон ссылки на неактивную страницу списка. Допускаемые для замены значения: link, pagenum";
		$this->prms['plInactiveTemplate']->DataType='char';
		$this->prms['plInactiveTemplate']->Value="<a href=\"--link--\">--pagenum--</a>";
		$this->prms['plInactiveTemplate']->Protected=false;

		$this->prms['plActiveTemplate']=new ConfigParam('plActiveTemplate');
		$this->prms['plActiveTemplate']->Description="Шаблон ссылки на текущую страницу списка. Допускаемые для замены значения: link, pagenum";
		$this->prms['plActiveTemplate']->DataType='char';
		$this->prms['plActiveTemplate']->Value="<a href=\"--link--\" style=\"color:red;\">--pagenum--</a>";
		$this->prms['plActiveTemplate']->Protected=false;

		$this->prms['plDevider']=new ConfigParam('plDevider');
		$this->prms['plDevider']->Description="Символы-разделители ссылок на страницы";
		$this->prms['plDevider']->DataType='char';
		$this->prms['plDevider']->Value="&nbsp;|&nbsp;";
		$this->prms['plDevider']->Protected=false;

		$this->prms['AntiFloodTO']=new ConfigParam('AntiFloodTO');
		$this->prms['AntiFloodTO']->Description="Таймаут (еденицы измерения - секунды) перед повторной отправкой сообщения с одного и того же IP-адреса.";
		$this->prms['AntiFloodTO']->DataType='int';
		$this->prms['AntiFloodTO']->Value=20;
		$this->prms['AntiFloodTO']->Protected=false;

		$this->prms['AntiFloodMessage']=new ConfigParam('AntiFloodMessage');
		$this->prms['AntiFloodMessage']->Description="Сообщение о срабатывании антифлуд-фильтра.";
		$this->prms['AntiFloodMessage']->DataType='memo';
		$this->prms['AntiFloodMessage']->Value="Извините, но ваше сообщение не может быть записано. Менее 20-ти секунд назад с вашего адреса уже было оставлено сообщение.";
		$this->prms['AntiFloodMessage']->Protected=false;

		$this->prms['DateFormat']=new ConfigParam('DateFormat');
		$this->prms['DateFormat']->Description="Формат вывода дат. (http://www.php.net/manual/en/function.date.php)";
		$this->prms['DateFormat']->DataType='char';
		$this->prms['DateFormat']->Value="d.m.Y";
		$this->prms['DateFormat']->Protected=false;


		$this->prms['messageform']=new ConfigParam('messageform');
		$this->prms['messageform']->Description="Форма для отправки сообщения в раздел. Требуемые input-таги: author, message. Допускаемые для замены значения: form (&lt;form name=gbform ... &gt;), displayname (отображаемое имя пользователя, зашедшего на сайт - в случае использования системы авторизации)";
		$this->prms['messageform']->DataType='memo';
		$this->prms['messageform']->Value="<table border=0 cellpadding=1 cellspacing=1>--form--" .
			"<tr><td align=right width=80>Представьтесь:</td><td align=left><input type=text class=text name=author size=30 maxlength=150 value=\"--displayname--\"></td></tr>" .
			"<tr><td align=right valign=top>Отзыв:</td><td align=left><textarea name=message cols=40 rows=4></textarea></td></tr>" .
			"<tr><td></td><td align=left><a href=\"javascript:document.forms['gbform'].submit();\">высказать</a></tr>" .
			"</form></table>";
		$this->prms['messageform']->Protected=false;

		$this->prms["moderate.mode"]=new ConfigParam("moderate.mode");
		$this->prms["moderate.mode"]->Description="Премодерируемый режим работы (пользовательские записи не публикуются пока модератор не разрешит публикацию).";
		$this->prms["moderate.mode"]->DataType="bool";
		$this->prms["moderate.mode"]->Value=false;
		$this->prms["moderate.mode"]->Protected=false;
	
		$this->prms["moderator.mail"]=new ConfigParam("moderator.mail");
		$this->prms["moderator.mail"]->Description="Адрес электронной почты модератора";
		$this->prms["moderator.mail"]->DataType="char";
		$this->prms["moderator.mail"]->Value="webmaster@" . $SiteMainURL;
		$this->prms["moderator.mail"]->Protected=false;
	
		$this->prms["moderator.notify"]=new ConfigParam("moderator.notify");
		$this->prms["moderator.notify"]->Description="Уведомлять модератора о новых записях в гостевой книге";
		$this->prms["moderator.notify"]->DataType="bool";
		$this->prms["moderator.notify"]->Value=false;
		$this->prms["moderator.notify"]->Protected=false;
	
		$this->prms["moderator.notify.subject"]=new ConfigParam("moderator.notify.subject");
		$this->prms["moderator.notify.subject"]->Description="Тема письма с уведомлением о новой записи в гостевой книге";
		$this->prms["moderator.notify.subject"]->DataType="char";
		$this->prms["moderator.notify.subject"]->Value="new record in guestbook";
		$this->prms["moderator.notify.subject"]->Protected=false;
	
		$this->prms["moderator.notify.body"]=new ConfigParam("moderator.notify.body");
		$this->prms["moderator.notify.body"]->Description="Тело письма с уведомлением о новой записи в гостевой книге. Допускаеме для замены значения: author, text";
		$this->prms["moderator.notify.body"]->DataType="memo";
		$this->prms["moderator.notify.body"]->Value="На сайте $SiteMainURL появилась новая запись в гостевой книге.\r\n--author--:\r\n--text--";
		$this->prms["moderator.notify.body"]->Protected=false;
	
	        if(isset($SAmodsArray["auth"])){
			$this->prms["only.authorized"]=new ConfigParam("only.authorized");
			$this->prms["only.authorized"]->Description="Разрешить доступ к гостевой книге только авторизованным пользователям.";
			$this->prms["only.authorized"]->DataType="bool";
			$this->prms["only.authorized"]->Value=false;
			$this->prms["only.authorized"]->Protected=false;
		};

		$this->gbTable="mod_gb";
		$this->ListingSize=0;

		$this->DropTablesScript="DROP TABLE IF EXISTS `$this->gbTable`\n";
		$this->CreateTablesScript="CREATE TABLE `$this->gbTable` (\n" . 
			"`id` int(11) NOT NULL auto_increment,\n" .
			"`node` int(11) NOT NULL default '0',\n" .
			"`writedate` int(11) NOT NULL default '0',\n" .
			"`author` varchar(250) NOT NULL default '',\n" .
			"`text` text NOT NULL,\n" .
			"`response` text NOT NULL,\n" .
			"`remoteip` varchar(20) NOT NULL default '',\n" .
			"`deleted` int(11) NOT NULL default '0',\n" .
			"`siteuserid` int(11) NOT NULL default '0',\n" .
			"`visible` int(11) NOT NULL default '0',\n" .
			"PRIMARY KEY  (`id`)\n" .
			")";
	}

	function ClientScript($theNode, $theFormPrefix, $thePage=1){
		$LocalthePage=$thePage;
	  	$retVal='';
		$ModifedFormPrefix=substr($theFormPrefix,0,strlen($theFormPrefix)-1);
		$ModifedFormPrefix.=' name="mod_gb_action_form">';
		$retVal.=$ModifedFormPrefix . "<input type=hidden name=mod_action value=\"\"><input type=hidden name=id value=0><input type=hidden name=page value=$LocalthePage><input type=hidden name=param1 value=\"\"></form>";
		$retVal.="<script>";
		$retVal.="function mod_gb_action(theAction,thePage,theParam1){";
		$retVal.="document.forms['mod_gb_action_form'].mod_action.value=theAction;\n";
		$retVal.="if(thePage)document.forms['mod_gb_action_form'].page.value=thePage;";
		$retVal.="if(theParam1)document.forms['mod_gb_action_form'].param1.value=theParam1;";
		$retVal.="document.forms['mod_gb_action_form'].submit();\n";
		$retVal.="};";
		$retVal.="</script>";
		return $retVal;
	}

	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
		global $HTTP_POST_VARS;
		$retVal='';
		$mod_action=$HTTP_POST_VARS["mod_action"];
		$PageNum=$HTTP_POST_VARS['page'];
		$PageNum=($PageNum>0)?$PageNum:1;
		$retVal.="<table border=0 cellpadding=0 cellspacing=0>" . $this->ClientScript($theNode, $theFormPrefix, $PageNum) . "</table>";
		if($mod_action=="edit"){
			$id=$HTTP_POST_VARS["param1"];
			$retVal.=$this->MakeAdminEditorScreen($theNode,$theFormPrefix,$id,$PageNum);
		};

		if($mod_action=="delete"){
			$sql="update `" . $this->gbTable . "` set `deleted`=1 where `id`=" . $HTTP_POST_VARS["param1"];
			$this->dbc->sql_query($sql);
		};

		if($mod_action=="update"){
			$author=(isset($HTTP_POST_VARS["author"]))?$HTTP_POST_VARS["author"]:"";
			$text=(isset($HTTP_POST_VARS["text"]))?$HTTP_POST_VARS["text"]:"";
			$response=(isset($HTTP_POST_VARS["response"]))?$HTTP_POST_VARS["response"]:"";
			$visible=($HTTP_POST_VARS["visible"]=="on")?1:0;
			$sql="update `" . $this->gbTable . "` set `author`='$author', `text`='$text', `response`='$response', `visible`=$visible where `id`=" . $HTTP_POST_VARS["id"];
			$this->dbc->sql_query($sql);
		};

		$retVal.=$this->MakeAdminItemsList($theNode,$theFormPrefix,$PageNum);
		return $retVal;
	}

    	function MakeAdminItemsList($theNode,$theFormPrefix,$thePage){
		$PageSize=$this->prms['AdminPageSize']->Value;
		$PageNum=$thePage;
		$retVal=drwTableBegin('100%','') . "<tr><td class=colheader colspan=2>модерирование записей в гостевой книге</td></tr>";
		$sql="select `id`, `node`, `writedate`, `author`, `text`, `response`, `remoteip`, `deleted` from `" . $this->gbTable . "` where `deleted`<>1 and `node`=$theNode order by `writedate` desc";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror["message"]);
		};
		$tdclass="data1";
		$counter=0;
		while($row=$this->dbc->sql_fetchrow()){
			$counter++;
			if(($counter>(($PageNum-1)*$PageSize))&&($counter<=(($PageNum)*$PageSize))){
				$wdate=date("d.m.Y H:i",$row['writedate']);
				$author=$row['author'];
				$text=(strlen($row["text"])>0)?$row["text"]:'';
				$text=CutQuots($text);
				$text=preg_replace("/(\r)?\n/","<br>",$text);
				$response=(strlen($row["response"])>0)?$row["response"]:'';
				$response=CutQuots($response);
				$response=preg_replace("/(\r)?\n/","<br>",$response);
				$retVal.="<tr><td class=$tdclass><strong>[$wdate] " . CutQuots($author) . "</strong>, IP-адрес: " . $row["remoteip"];
				$retVal.="<br><strong>текст:&nbsp;</strong>$text";
				$retVal.="<br><strong>комметарий:&nbsp;</strong>$response";
				$retVal.="<br><input type=button class=button value=\"редактировать\" onclick=\"mod_gb_action('edit',$thePage," . $row['id'] . ")\">";
				$retVal.="&nbsp;<input type=button class=button value=\"удалить\" onclick=\"mod_gb_action('delete',$thePage," . $row['id'] . ")\">";
				$retVal.="</td></tr>";
				$tdclass=($tdclass=="data1")?"data2":"data1";
			};
		};
		$retVal.=drwTableEnd();
		$PagerStr='';
		$lastPage=($counter%$PageSize==0)?0:1;
		$lastPage+=($counter/$PageSize);
		if($lastPage>=2){
			$PagerStr.=drwTableBegin('100%','') . "<tr><td width=10% nowrap align=right class=colheader>страница:</td><td class=data1>";
			for($fcounter=1;$fcounter<=$lastPage;$fcounter++){
				if($fcounter==$PageNum){
					$PagerStr.="<a style=\"color:gray;\">$fcounter</a> ";
				}else{
					$PagerStr.="<a href=\"javascript:mod_gb_action('',0,$fcounter)\">$fcounter</a> ";
				};
			};
			$PagerStr.="</td></tr>" . drwTableEnd();
		};
		$retVal.=$PagerStr;

		return $retVal;
	}  	

    	function MakeAdminEditorScreen($theNode,$theFormPrefix,$theID,$thePage){
		$retVal=drwTableBegin('100%','') . "<tr><td class=colheader colspan=2>редактирование записи в гостевой книге</td></tr>";
		$sql="select `id`, `node`, `writedate`, `author`, `text`, `response`, `remoteip`, `deleted`, `visible`, `siteuserid` from `" . $this->gbTable . "` where `deleted`<>1 and `node`=$theNode and `id`=" . $theID;
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror["message"]);
		};
		if($row=$this->dbc->sql_fetchrow()){
			$wdate=date("d.m.Y H:i",$row["writedate"]);
			$author=$row['author'];
			$text=(strlen($row["text"])>0)?$row["text"]:'';
			$text=CutQuots($text);
			$response=(strlen($row["response"])>0)?$row["response"]:'';
			$response=CutQuots($response);
			$retVal.=$theFormPrefix ."<input type=hidden name=mod_action value=update><input type=hidden name=id value=$theID>";
			$retVal.="<tr><td class=colheader colspan=2>[$wdate], IP-адрес: " . $row["remoteip"] . "</td></tr>";
			$rostr=($row["siteuserid"]>0)?" readonly":"";
			$retVal.="<tr><td class=data1 align=right>автор:</td><td class=data1 align=left><input type=text class=text size=50 name=author value=\"" . CutQuots($author) . "\"$rostr></td></tr>";
			$retVal.="<tr><td class=data2 valign=top align=right>текст:</td><td class=data2 align=left><textarea name=text cols=60 rows=5>$text</textarea></td></tr>";
			$retVal.="<tr><td class=data1 valign=top align=right>комментарий:</td><td class=data1 align=left><textarea name=response cols=60 rows=5>$response</textarea></td></tr>";
			$checked=($row["visible"]==1)?" checked":"";
			$retVal.="<tr><td class=data2 valign=top align=right>доступность на сайте:</td><td class=data2 align=left><input type=checkbox name=visible $checked></td></tr>";
			$retVal.="<tr><td class=data1 colspan=2 align=center><input type=submit class=button value=\"обновить\"></td></tr>";
			$retVal.="</form>";
		};
		$retVal.=drwTableEnd();
		return $retVal;
	}  	

	function MakeUserOuput($theNode, $theFormPrefix){
		global $HTTP_POST_VARS, $HTTP_GET_VARS, $client_ip, $SessionSettings, $coreParams;
		$retArray=array();
		$retVal="";
		$PageNum=$HTTP_GET_VARS['page'];
		$PageNum=($PageNum>0)?$PageNum:1;
		if($HTTP_POST_VARS["mod_action"]=="write"){
			$afb=time()-$this->prms['AntiFloodTO']->Value;
			$sql="select `id`, `author`,`writedate` from `" . $this->gbTable . "` where `node`=$theNode and `remoteip`='$client_ip' and `deleted`<>1 and `writedate`>$afb";
			if(!$this->dbc->sql_query($sql)){
			    $sqlerror=$this->dbc->sql_error();
			    die($sqlerror['message']);
			};
			if($this->dbc->sql_numrows()!=0){
				$this->UserFinalOutput.="<script>alert('" . CutQuots($this->prms["AntiFloodMessage"]->Value) . "')</script>";
			}else{
				$author=$HTTP_POST_VARS["author"];
				$text=$HTTP_POST_VARS["message"];
				$siteuserid=($SessionSettings["siteuserid"]>0)?$SessionSettings["siteuserid"]:0;
				$visible=($this->prms["moderate.mode"]->Value)?0:1;
				$sql="insert into `" . $this->gbTable . "` (`node`, `writedate`, `author`, `text`, `response`, `deleted`, `remoteip`, `siteuserid`,`visible`) values ('$theNode'," . time() . ",'$author','$text','',0,'$client_ip',$siteuserid, $visible)";
				if(strlen($text)>0){
				    if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror['message']);
				    };
				};
				if($this->prms["moderator.notify"]->Value){
					$fromemail=$coreParams["webmasteremail"]->Value;
					$mailheader="MIME-Version: 1.0\nContent-type: text/plain; charset=windows-1251\nDate: " . gmdate('D, d M Y H:i:s', time()) . " UT\nX-Priority: 3\nX-MSMail-Priority: Normal\nX-Mailer: PHP\n";
					$mailheader.="From: " . $fromemail . "<$fromemail>\n";
					$MessageTS=$this->prms["moderator.notify.body"]->Value;
					$MessageTS=str_replace("--author--",$author,$MessageTS);
					$MessageTS=str_replace("--text--",$text,$MessageTS);
					if(mail($this->prms["moderator.mail"]->Value,$this->prms["moderator.notify.subject"]->Value,$MessageTS,$mailheader)){
						//;
					};
				};
				header ("Location: $theFormPrefix&page=$thePage&written");
				return;
			};
		};
		$itemslist=$this->MakeUserListing($theNode, $theFormPrefix, $PageNum);
		$pageslist=$this->MakePageChanger($theNode, $theFormPrefix, $PageNum);
		$messageform=$this->prms["messageform"]->Value;
		$messageform=str_replace("--form--","<form method=post action=\"$theFormPrefix&page=$PageNum\" name=gbform><input type=hidden name=mod_action value=write>",$messageform);
		$displayname=($SessionSettings["siteuserid"]>0)?CutQuots($SessionSettings["siteuserdisplayname"]):"";
		$messageform=str_replace("--displayname--",$displayname,$messageform);
		$retVal=$this->prms['PageTemplate']->Value;
		$retVal=str_replace("--itemslist--",$itemslist,$retVal);
		$retVal=str_replace("--pager--",$pageslist,$retVal);
		$retVal=str_replace("--messageform--",$messageform,$retVal);
		$retVal.=$this->UserFinalOutput;
		$retArray[0]=$retVal;
		return $retArray;
	}	

	function MakeUserListing($theNode, $theFormPrefix, $thePage){
		global $SAmodsArray, $SessionSettings;
		$retVal='';
		$PageSize=$this->prms["PageSize"]->Value;
		$ItemTemplate=$this->prms["ItemTemplate"]->Value;
		$Devider=$this->prms["ItemsDevider"]->Value;
		$PageNum=$thePage;

		$sql="select `id`, `node`, `writedate`, `author`, `text`, `response`, `remoteip`, `deleted`, `siteuserid` from `" . $this->gbTable . "` where `deleted`<>1 and `node`=$theNode and `visible`=1 order by `writedate` desc";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		};
		$counter=0;
		$OneItem='';
		while($row=$this->dbc->sql_fetchrow()){
			$counter++;
			$shifted=$counter;
			$InPage=(($shifted>(($PageNum-1)*$PageSize))&&($shifted<=(($PageNum)*$PageSize)));
			if($InPage){
				$response=CutQuots($row["response"]);
				if(strlen($response)>0){
					$response=preg_replace("/(\r)?\n/","<br>",$response);
					$response=str_replace("--text--",$response,$this->prms['ResponseTemplate']->Value);
				};
				$text=CutQuots($row["text"]);
				$text=preg_replace("/(\r)?\n/","<br>",$text);
				$OneItem=$ItemTemplate;
				$OneItem=str_replace("--date--",date($this->prms["DateFormat"]->Value,$row["writedate"]),$OneItem);
				$OneItem=str_replace("--text--",$text,$OneItem);
				$author=(($row["siteuserid"]>0)&&($SessionSettings["siteuserid"]>0))?"<a href=\"" . $SAmodsArray["auth"]->interfaceScript . "?pm&create=" . $row["siteuserid"] . "\">" . CutQuots($row["author"]) . "</a>":CutQuots($row["author"]);
				$OneItem=str_replace("--author--",$author,$OneItem);
				$OneItem=str_replace("--response--",$response,$OneItem);
				$retVal.=$OneItem . $Devider;
			};
		};
		if(strlen($retVal)>strlen($Devider))$retVal=substr($retVal,0,(strlen($retVal)-strlen($Devider)));
		$this->ListingSize=$shifted;
		return $retVal;
	}  	

	function MakePageChanger($theNode, $theFormPrefix, $thePage){
		$retVal='';
		$counter=$this->ListingSize;
		$PageSize=$this->prms["PageSize"]->Value;
		$PageNum=$thePage;
		$lastPage=($counter%$PageSize==0)?0:1;
		$PageHref='';
		$PageLink='';
		$Devider=$this->prms["plDevider"]->Value;
		$lastPage+=($counter/$PageSize);
		if($lastPage<2)return "";
		for($fcounter=1;$fcounter<=$lastPage;$fcounter++){
			$PageHref=$theFormPrefix . '&page=' . $fcounter;
			$PageLink=($fcounter==$PageNum)?$this->prms["plActiveTemplate"]->Value:$this->prms["plInactiveTemplate"]->Value;
			$PageLink=str_replace("--link--",$PageHref,$PageLink);
			$PageLink=str_replace("--pagenum--",$fcounter,$PageLink);
			$retVal.=$PageLink . $Devider;
		};
		if(strlen($retVal)>strlen($Devider))$retVal=substr($retVal,0,(strlen($retVal)-strlen($Devider)));
		if(strlen($retVal)>0)$retVal=str_replace("--pageslist--",$retVal,$this->prms['plTemplate']->Value);
		return $retVal;
	}

    function SearchString($theText){
	$retVal=array();
	$sql="select `id`, `node`, `writedate`, `author`, `text`, `response`, `remoteip`, `deleted` from `" . $this->gbTable . "`";
	$sql.=" where `visible`=1 and `deleted`<>1 and (UPPER(`text`) like UPPER('%" . str_replace("'","''",$theText) . "%') or UPPER(`response`) like UPPER('%" . str_replace("'","''",$theText) . "%'))";
	$sql.=" order by `writedate` desc";
	if(!$this->dbc->sql_query($sql)){
		$sqlerror=$this->dbc->sql_error();
		die($sqlerror['message']);
	};
	while($row=$this->dbc->sql_fetchrow()){
	  	$foundText=striphtml($row['text']);
	    	$charpos=strpos(strtoupper($foundText),strtoupper($theText));
	  	if($charpos>=0){
	  		$foundText=$foundText;
	  	}else{
		  	$foundText=striphtml($row['response']);
	    		$charpos=strpos(strtoupper($foundText),strtoupper($theText));
	  	};
	    	$textPreview='';
	    	if($charpos>=0){
	    		$textPreview="<strong><u>" . substr($foundText,$charpos,strlen($theText)) . "</u></strong>";
			$BeginPos=(($charpos-100)>0)?($charpos-100):0;
			$textPreview="... " . substr($foundText,$BeginPos,$charpos-$BeginPos) . $textPreview . substr($foundText,$charpos+strlen($theText),100) . " ...";
		};
		$retVal[$counter]=new cslSearchResult();
		$retVal[$counter]->Node=$row['node'];
		$retVal[$counter]->LinkName='';
		$retVal[$counter]->ResultPreview=$textPreview;
		$retVal[$counter]->QSParams='';
		$counter++;
	};
	return $retVal;
    }

	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->gbTable`;
			CREATE TABLE `$this->gbTable` (
			  `id` int(11) NOT NULL auto_increment,
			  `node` int(11) NOT NULL default '0',
			  `writedate` int(11) NOT NULL default '0',
			  `author` varchar(250) NOT NULL default '',
			  `text` text NOT NULL,
			  `response` text NOT NULL,
			  `remoteip` varchar(20) NOT NULL default '',
			  `deleted` int(11) NOT NULL default '0',
			  `siteuserid` int(11) NOT NULL default '0',
			  `visible` int(11) NOT NULL default '0',
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



$theGuestbookModule=new clsGuestbookModule("guestbook","гостевая книга",$db);
$modsArray["guestbook"]=$theGuestbookModule;

?>