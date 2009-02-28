<?php
class clsNewsModule extends clsModule{
	function clsNewsModule($modName,$modDName,$dbconnector){
	    global $SAmodsArray;
	    parent::clsModule($modName,$modDName,$dbconnector);
	    $this->version="1.0.10";
	    $this->helpstring="<p>Модуль реализует ленту новостей.</p>";
	    $this->SearchAble=true;

	    if(isset($SAmodsArray["subscribe"])){
		    $this->prms["mailtoclients"]=new ConfigParam("mailtoclients");
		    $this->prms["mailtoclients"]->Description="Использовать систему подписки на новости.";
		    $this->prms["mailtoclients"]->DataType="bool";
		    $this->prms["mailtoclients"]->Value=false;
		    $this->prms["mailtoclients"]->Protected=false;
	    };

	    $this->prms["PageSize"]=new ConfigParam("PageSize");
	    $this->prms["PageSize"]->Description="Количество отображаемых новостей на странице.";
	    $this->prms["PageSize"]->DataType="int";
	    $this->prms["PageSize"]->Value=20;
	    $this->prms["PageSize"]->Protected=false;

	    $this->prms["AdminPageSize"]=new ConfigParam("AdminPageSize");
	    $this->prms["AdminPageSize"]->Description="Количество отображаемых новостей на странице модератора.";
	    $this->prms["AdminPageSize"]->DataType="int";
	    $this->prms["AdminPageSize"]->Value=50;
	    $this->prms["AdminPageSize"]->Protected=true;

	    $this->prms["FirstPageDays"]=new ConfigParam("FirstPageDays");
	    $this->prms["FirstPageDays"]->Description="Размер первой страницы новостей в днях. Количество последних дней за которые следует формировать первую страницу новостей. Если значение равно \"0\", то размер первой страницы будет определяться заданным количеством новостей на странице.";
	    $this->prms["FirstPageDays"]->DataType="int";
	    $this->prms["FirstPageDays"]->Value=0;
	    $this->prms["FirstPageDays"]->Protected=false;

	    $this->prms["PageTemplate"]=new ConfigParam("PageTemplate");
	    $this->prms["PageTemplate"]->Description="Шаблон отображения страницы новостей. Допускаемое для замены значение: itemslist, pager, calendar, subscribeform";
	    $this->prms["PageTemplate"]->DataType='memo';
	    $this->prms["PageTemplate"]->Value='<ul class="news">--itemslist--</ul>--pager--';
	    $this->prms["PageTemplate"]->Protected=false;

    	    $this->prms["MinislotTemplate"]=new ConfigParam("MinislotTemplate");
	    $this->prms["MinislotTemplate"]->Description="Шаблон отображения вспомогательного блока. Допускаемые для замены значения: dateselector, calendar, subscribeform";
	    $this->prms["MinislotTemplate"]->DataType='memo';
	    $this->prms["MinislotTemplate"]->Value="<center>--dateselector----calendar--<br>--subscribeform--<br></center>";
	    $this->prms["MinislotTemplate"]->Protected=false;


    	    $this->prms["plTemplate"]=new ConfigParam("plTemplate");
	    $this->prms["plTemplate"]->Description="Шаблон отображения списка страниц новостей. Заменяет pager в шаблоне PageTemplate. Допускаемое для замены значение: pageslist";
	    $this->prms["plTemplate"]->DataType="memo";
	    $this->prms["plTemplate"]->Value="<tr><td align=right><br>страница:&nbsp;--pageslist--</td></tr>";
	    $this->prms["plTemplate"]->Protected=false;


	    $this->prms["ItemTemplate"]=new ConfigParam("ItemTemplate");
	    $this->prms["ItemTemplate"]->Description="Шаблон отображения одной новости внутри шаблона PageTemplate. Допускаемые для замены значения: date, subject, text, hreffull, linkfull, linkcomments, author, striptext";
	    $this->prms["ItemTemplate"]->DataType='memo';
	    $this->prms["ItemTemplate"]->Value='<li><small>--date--</small><a href="--hreffull--">--subject--</a></li>';
	    $this->prms["ItemTemplate"]->Protected=false;

	    $this->prms["ItemLinkFullTemplate"]=new ConfigParam("ItemLinkFullTemplate");
	    $this->prms["ItemLinkFullTemplate"]->Description="Шаблон ссылки на полную новость. Используется внутри шаблона ItemTemplate. Допускаемые для замены значения: href, subject, striptext, text";
	    $this->prms["ItemLinkFullTemplate"]->DataType='char';
	    $this->prms["ItemLinkFullTemplate"]->Value='<a href="--href--">узнать подробности</a>';
	    $this->prms["ItemLinkFullTemplate"]->Protected=false;

	    $this->prms["ItemLinkCommentsTemplate"]=new ConfigParam("ItemLinkCommentsTemplate");
	    $this->prms["ItemLinkCommentsTemplate"]->Description="Шаблон ссылки на страницу комментирования новости. Используется внутри шаблона ItemTemplate. Допускаемые для замены значения: href, subject, count";
	    $this->prms["ItemLinkCommentsTemplate"]->DataType="char";
	    $this->prms["ItemLinkCommentsTemplate"]->Value="<tr><td align=right style=\"font-size:11px;\"><a href=\"--href--\">комментарии (--count--)&gt;&gt;</a></td></tr>";
	    $this->prms["ItemLinkCommentsTemplate"]->Protected=false;

	    $this->prms["ItemsDevider"]=new ConfigParam("ItemsDevider");
	    $this->prms["ItemsDevider"]->Description="html-код разделяющий отдельные новости на странице";
	    $this->prms["ItemsDevider"]->DataType='memo';
	    $this->prms["ItemsDevider"]->Value="";
	    $this->prms["ItemsDevider"]->Protected=false;

	    $this->prms["ItemFullTemplate"]=new ConfigParam("ItemFullTemplate");
	    $this->prms["ItemFullTemplate"]->Description='Шаблон отображения полной новости. Допускаемые для замены значения: date, subject, text, author, comments, hreflist (адрес возвращающий на список новостей)';
	    $this->prms["ItemFullTemplate"]->DataType='memo';
	    $this->prms["ItemFullTemplate"]->Value='<small>--date--</small> <h2>--subject--</h2>--text--<p><a href="--hreflist--">назад</a></p>';
	    $this->prms["ItemFullTemplate"]->Protected=false;
	    
	    $this->prms["plInactiveTemplate"]=new ConfigParam("plInactiveTemplate");
	    $this->prms["plInactiveTemplate"]->Description="Шаблон ссылки на неактивную страницу новостей. Допускаемые для замены значения: link, pagenum";
	    $this->prms["plInactiveTemplate"]->DataType='char';
	    $this->prms["plInactiveTemplate"]->Value="<a href=\"--link--\">--pagenum--</a>";
	    $this->prms["plInactiveTemplate"]->Protected=false;

	    $this->prms["plActiveTemplate"]=new ConfigParam("plActiveTemplate");
	    $this->prms["plActiveTemplate"]->Description="Шаблон ссылки на текущую страницу новостей. Допускаемые для замены значения: link, pagenum";
	    $this->prms["plActiveTemplate"]->DataType="char";
	    $this->prms["plActiveTemplate"]->Value="<a href=\"--link--\" style=\"color:black;\">--pagenum--</a>";
	    $this->prms["plActiveTemplate"]->Protected=false;

	    $this->prms["plDevider"]=new ConfigParam("plDevider");
	    $this->prms["plDevider"]->Description="Символы-разделители ссылок на страницы";
	    $this->prms["plDevider"]->DataType="char";
	    $this->prms["plDevider"]->Value="&nbsp;&nbsp;";
	    $this->prms["plDevider"]->Protected=false;
	    
	    $this->prms["UseComments"]=new ConfigParam("UseComments");
	    $this->prms["UseComments"]->Description="Использовать систему комментирования новостей.";
	    $this->prms["UseComments"]->DataType="bool";
	    $this->prms["UseComments"]->Value=false;
	    $this->prms["UseComments"]->Protected=false;

	    $this->prms["CommentsTemplate"]=new ConfigParam("CommentsTemplate");
	    $this->prms["CommentsTemplate"]->Description="Шаблон отображения комментариев. Заполняет шаблонное выражение --comments-- в шаблоне ItemFullTemplate. Допускаемые для замены значения: itemslist, commentform";
	    $this->prms["CommentsTemplate"]->DataType="memo";
	    $this->prms["CommentsTemplate"]->Value="<tr><td colspan=2 class=colheader align=center><a name=comments></a>комментарии</td></tr><tr><td colspan=2 class=data1><ul>--itemslist--</ul><br><center>--commentform--</center></td></tr>";
	    $this->prms["CommentsTemplate"]->Protected=false;

	    $this->prms["CommentsItemTemplate"]=new ConfigParam("CommentsItemTemplate");
	    $this->prms["CommentsItemTemplate"]->Description="Шаблон отображения одного комментария в списке комментариев. Заполняет шаблонное выражение --items-- в шаблоне CommentsTemplate. Допускаемые для замены значения: date, author, email, comment";
	    $this->prms["CommentsItemTemplate"]->DataType="memo";
	    $this->prms["CommentsItemTemplate"]->Value="<li><u>--date--, --author--</u><br>--comment--</li>\r\n";
	    $this->prms["CommentsItemTemplate"]->Protected=false;

	    $this->prms["CommentsItemsDevider"]=new ConfigParam("CommentsItemsDevider");
	    $this->prms["CommentsItemsDevider"]->Description="html-код разделяющий отдельные комментарии в списке комментариев";
	    $this->prms["CommentsItemsDevider"]->DataType="memo";
	    $this->prms["CommentsItemsDevider"]->Value="<hr size=1 style=\"color:#9F4B1C\">";
	    $this->prms["CommentsItemsDevider"]->Protected=false;

	    $this->prms["CommentsFormTemplate"]=new ConfigParam("CommentsFormTemplate");
	    $this->prms["CommentsFormTemplate"]->Description="Форма для комментирования новостей. Заполняет шаблонное выражение --commentform-- в шаблоне CommentsTemplate. Допускаемые для замены значения: form ( <form method....> )";
	    $this->prms["CommentsFormTemplate"]->DataType="memo";
	    $this->prms["CommentsFormTemplate"]->Value="<table width=90% >--form--<input type=hidden name=action value=comment><tr><td class=colheader colspan=2 align=center>оставить комментарий</td></tr><tr><td class=data1 align=right>представьтесь:</td><td class=data1 align=left><input type=text class=text name=author size=40></td></tr><tr><td class=data1 align=right>ваш e-mail:</td><td class=data1 align=left><input type=text class=text name=email size=40></td></tr><tr><td class=data1 align=right valign=top>комментарий:</td><td class=data1 align=left valign=top><textarea name=comment cols=40 rows=7></textarea></td></tr><tr><td colspan=2 align=center class=data1><input type=submit class=button value=\"записать\"></td></tr></form></table>";
	    $this->prms["CommentsFormTemplate"]->Protected=false;

	    $this->prms["AntiFloodTO"]=new ConfigParam("AntiFloodTO");
	    $this->prms["AntiFloodTO"]->Description="Таймаут (еденицы измерения - секунды) перед повторным комментированием новости с одного и того же IP-адреса.";
	    $this->prms["AntiFloodTO"]->DataType="int";
	    $this->prms["AntiFloodTO"]->Value=20;
	    $this->prms["AntiFloodTO"]->Protected=false;

	    $this->prms["AntiFloodMessage"]=new ConfigParam("AntiFloodMessage");
	    $this->prms["AntiFloodMessage"]->Description="Сообщение о срабатывании антифлуд-фильтра.";
	    $this->prms["AntiFloodMessage"]->DataType="memo";
	    $this->prms["AntiFloodMessage"]->Value="Извините, но ваш комментарий не может быть записан. Менее 20-ти секунд назад с вашего адреса уже был записан комментарий.";
	    $this->prms["AntiFloodMessage"]->Protected=false;


	    $this->prms["DateFormat"]=new ConfigParam("DateFormat");
	    $this->prms["DateFormat"]->Description="Формат вывода дат. (http://www.php.net/manual/en/function.date.php)";
	    $this->prms["DateFormat"]->DataType="char";
	    $this->prms["DateFormat"]->Value="d.m.Y";
	    $this->prms["DateFormat"]->Protected=false;

	    $this->prms["CalendarTemplate"]=new ConfigParam("CalendarTemplate");
	    $this->prms["CalendarTemplate"]->Description="Шаблон вывода календаря. доступные для замены значения: curmon, dates, monprev, monnext;";
	    $this->prms["CalendarTemplate"]->DataType="memo";
	    $this->prms["CalendarTemplate"]->Value="<table border=0><tr><td colspan=7 align=center><table width=100% border=0><td align=left style=\"font-weight:bold;\"><a href=\"--monprev--\">&lt;&lt;</a></td><td style=\"font-weight:bold;\" align=center>--curmon--</td><td style=\"font-weight:bold;\" align=right><a href=\"--monnext--\">&gt;&gt;</a></td></tr></table></tr><tr><td class=calcol align=right>пн</td><td class=calcol align=right>вт</td><td class=calcol align=right>ср</td><td class=calcol align=right>чт</td><td class=calcol align=right>пт</td><td class=calcol style=\"color:#ED0000\" align=right>сб</td><td class=calcol style=\"color:#ED0000\" align=right>вс</td></tr>--dates--</table>";
	    $this->prms["CalendarTemplate"]->Protected=false;

	    $this->prms["CalendarDateLink"]=new ConfigParam("CalendarDateLink");
	    $this->prms["CalendarDateLink"]->Description="Шаблон вывода даты календаря, содержащей новости. доступные для замены значения: link, mdate;";
	    $this->prms["CalendarDateLink"]->DataType="char";
	    $this->prms["CalendarDateLink"]->Value="<td align=right><a href=\"--link--\" style=\"color:#003366\">--mdate--</a></td>";
	    $this->prms["CalendarDateLink"]->Protected=false;

    	    $this->prms["CalendarDateCurrent"]=new ConfigParam("CalendarDateCurrent");
	    $this->prms["CalendarDateCurrent"]->Description="Шаблон вывода даты календаря, новости от которой отображаются в данный момент. доступные для замены значения: link, mdate;";
	    $this->prms["CalendarDateCurrent"]->DataType="char";
	    $this->prms["CalendarDateCurrent"]->Value="<td align=right><a href=\"--link--\" style=\"color:#C20000\">--mdate--</a></td>";
	    $this->prms["CalendarDateCurrent"]->Protected=false;


	    $this->prms["CalendarDate"]=new ConfigParam("CalendarDate");
	    $this->prms["CalendarDate"]->Description="Шаблон вывода даты календаря, НЕ содержащей новости. доступные для замены значения: link, mdate;";
	    $this->prms["CalendarDate"]->DataType="char";
	    $this->prms["CalendarDate"]->Value="<td align=right ctyle=\"color:#727272\">--mdate--</td>";
	    $this->prms["CalendarDate"]->Protected=false;

    	    $this->prms["DateSelectTemplate"]=new ConfigParam("DateSelectTemplate");
	    $this->prms["DateSelectTemplate"]->Description="Шаблон меню выбора даты для отображения новостей. доступные для замены значения: day, month, year, golink, formname. Если вы хотите отказаться от выбора даты или месяца, то кроме удаления из шаблона заменяемых полей необходимо вписать html-таг для задания умлочания (например <input type=hidden name=day value=01>)";
	    $this->prms["DateSelectTemplate"]->DataType="memo";
	    $this->prms["DateSelectTemplate"]->Value="<table border=0><form name=--formname--><input type=hidden name=day value=01><tr><td>--month--</td><td>--year--</td><td><a href=\"--golink--\"><img src=simages/go.gif width=18 height=18 border=0></a></td></tr></form></table>";
	    $this->prms["DateSelectTemplate"]->Protected=false;

	    $this->TemplateValue="/--([a-zA-Z0-9]+)--/i";
	    $this->ListingSize=0;


	    $this->UserFinalOutput='';
	    $this->CurUserDate=0;

	    $this->newsTable='mod_news';
	    $this->commentsTable='mod_news_comments';
	    return $this;
	}

    function ClientScript($theNode, $theFormPrefix, $thePage=1){
	$LocalthePage=$thePage;
  	$retVal='';
	$ModifedFormPrefix=substr($theFormPrefix,0,strlen($theFormPrefix)-1);
	$ModifedFormPrefix.=' name="mod_news_action_form">';
	$retVal.=$ModifedFormPrefix . "<input type=hidden name=mod_action value=\"\"><input type=hidden name=id value=0><input type=hidden name=page value=$LocalthePage><input type=hidden name=param1 value=\"\"></form>";
	$retVal.="<script>";
	$retVal.="function mod_news_action(theAction,theID,thePage,theParam1){";
	$retVal.="document.forms['mod_news_action_form'].mod_action.value=theAction;\n";
	$retVal.="document.forms['mod_news_action_form'].id.value=theID;\n";
	$retVal.="if(thePage)document.forms['mod_news_action_form'].page.value=thePage;";
	$retVal.="if(theParam1)document.forms['mod_news_action_form'].param1.value=theParam1;";
	$retVal.="document.forms['mod_news_action_form'].submit();\n";
	$retVal.="};";
	$retVal.="</script>";
	return $retVal;
    }


    function MakeAdminOuput ($theNode, $theFormPrefix, $theSessionSettings){
	global $HTTP_POST_VARS, $SAmodsArray;
	$retVal='';
	$mod_future_action='insert';
	$mod_action=$HTTP_POST_VARS['mod_action'];
	$PageNum=$HTTP_POST_VARS['page'];
	$PageNum=($PageNum>0)?$PageNum:1;
	$CurrentID=$HTTP_POST_VARS['id'];
	$retVal.=$this->ClientScript($theNode, $theFormPrefix, $PageNum);
	$CurrentID=($CurrentID>0)?$CurrentID:0;
	if($mod_action=='insert'){
		$ShortrText=text_create_new();
		$rText=text_create_new();
		$Subject=(isset($HTTP_POST_VARS['subject']))?$HTTP_POST_VARS['subject']:'';
		$sql="insert into `" . $this->newsTable . "` (`node`, `author`, `subject`, `writedate`, `rtext`, `shortrtext`, `visible`, `deleted`) values ($theNode, '" . $theSessionSettings['login'] . "', '$Subject', " . time() . ", $rText, $ShortrText, 0, 0)";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		};
		$CurrentID=$this->dbc->sql_nextid();
		$mod_action='edit';
		$PageNum=1;
	};
	if($mod_action=='edit'){
		$mod_future_action='update';
		$retVal.=$this->MakeAdminEditorScreen($theNode,$theFormPrefix,$CurrentID,$PageNum);
	};


	if($mod_action=='comments_delete'){
		$sql="update `" . $this->commentsTable . "` set `deleted`=1 where `id`=" . $HTTP_POST_VARS['param1'];
		$this->dbc->sql_query($sql);
		$mod_action='comments';
	};

	if($mod_action=='comments'){
		$mod_future_action='comments';
		$retVal.=$this->MakeAdminCommentsScreen($theNode,$theFormPrefix,$CurrentID,$PageNum);
	};

	if($mod_action=="sendtoclients"){
		if(isset($SAmodsArray["subscribe"])){
			$mod_future_action='sendtoclients';
			$sql="select `nt`.`id` as `id`, `nt`.`node` as `node`, `nt`.`author` as `author`, `nt`.`subject` as `subject`, `nt`.`writedate` as `writedate`, `ft`.`text` as `text`,  `nt`.`showrtext` as `showrtext`, `st`.`text` as `shorttext`, `nt`.`visible` as `visible`, `nt`.`deleted` as `deleted`";
			$sql.=" from `" . $this->newsTable . "` as `nt`, `texts` as `ft`, `texts` as `st` where `nt`.`deleted`<>1 and `nt`.`visible`=1 and `ft`.`id` = `nt`.`rtext` and `nt`.`shortrtext`=`st`.`id` and `nt`.`node`=$theNode  and `nt`.`id`=$CurrentID";
			$sql.=" group by `nt`.`id`, `nt`.`node`, `nt`.`author`, `nt`.`subject`, `nt`.`writedate`, `ft`.`text`,  `nt`.`showrtext`, `st`.`text`, `nt`.`visible`, `nt`.`deleted`";
			if(!$this->dbc->sql_query($sql)){
			    $sqlerror=$this->dbc->sql_error();
			    die($sqlerror['message']);
			};
			if($row=$this->dbc->sql_fetchrow()){
				$sendText=($row['showrtext']==1)?$row['text']:$row['shorttext'];
				$sendText=striphtml($sendText);
				$sendKey="news_" . $row['id'];
				$sendDesc="Рассылка новости \"" . $row['subject'] . "\"";
				$sendSubj=$row["subject"];
				$retVal.="<a class=header href=\"javascript:mod_news_action('',0)\">[вернуться к списку новостей]</a><br>";
				$retVal.=$SAmodsArray["subscribe"]->MakeAdminOuput($theNode,$theFormPrefix,$sendKey,$sendDesc,$sendSubj,$sendText,"<input type=hidden name=id value=$CurrentID><input type=hidden name=mod_action value=\"$mod_action\">");
			};
		};
	};



	if($mod_action=='update'){
		$WriteDate=PostToDate('WriteDate');
		$Subject=(isset($HTTP_POST_VARS['subject']))?$HTTP_POST_VARS['subject']:'';
		$Author=(isset($HTTP_POST_VARS['author']))?$HTTP_POST_VARS['author']:'';
		$Visible=($HTTP_POST_VARS['Visible']=='on')?1:0;
		$Deleted=($HTTP_POST_VARS['delete']=='on')?1:0;
		$ShowrText=($HTTP_POST_VARS['ShowrText']=='on')?1:0;
		$sql="update `" . $this->newsTable . "` set `WriteDate`=$WriteDate, `Subject`='$Subject', `Author`='$Author', `Visible`=$Visible, `Deleted`=$Deleted, `ShowrText`=$ShowrText where `id`=" . $CurrentID . " and `node`=" . $theNode;
		if(!$this->dbc->sql_query($sql)){
		    $sqlerror=$this->dbc->sql_error();
		    die($sqlerror['message']);
		};
		$mod_future_action='insert';
	};
	if($mod_future_action=='insert'){
		$retVal.="<br><center>" . drwTableBegin('400','') . "<tr><td class=colheader colspan=2>добавить новость</td></tr>" . $theFormPrefix . "<input type=hidden name=mod_action value=\"" . $mod_future_action . "\">";
		$retVal.="<tr><td class=data1 align=right>Тема:</td><td class=data1 align=left><input type=text class=text size=60 name=subject></td></tr>";
		$retVal.="<tr><td class=data2 align=center colspan=2><input type=submit class=button value=\"добавить\"></td></tr>";
		$retVal.="</form>" . drwTableEnd() . "</center>";
		$retVal.="<br>";
		$retVal.=$this->MakeAdminNewsList($theNode,$theFormPrefix,$PageNum);
	};
	return $retVal;
    }

    function MakeAdminNewsList($theNode,$theFormPrefix,$thePage){
	$retVal='';
	$retVal.=drwTableBegin('100%','') . "<tr><td class=colheader colspan=5>существующие новости</td></tr>";
	$PageSize=$this->prms['AdminPageSize']->Value;
	$PageNum=$thePage;
	$sql="select `id`, `node`, `author`, `subject`, `writedate`, `rtext`, `shortrtext`, `visible`, `deleted` from `" . $this->newsTable . "` where `deleted`<>1 and `node`=" . $theNode . " order by `writedate` desc";
	if(!$this->dbc->sql_query($sql)){
	    $sqlerror=$this->dbc->sql_error();
	    die($sqlerror['message']);
	};
	$counter=0;
	$tdclass='data1';
	while($row=$this->dbc->sql_fetchrow()){
		$counter++;
		if(($counter>(($PageNum-1)*$PageSize))&&($counter<=(($PageNum)*$PageSize))){
			$wdate=date("d.m.Y",$row['writedate']);
			$retVal.="<tr><td class=$tdclass align=center>[$wdate]</td><td class=$tdclass>" . CutQuots($row['subject']) . "</td>";
			$retVal.="<td class=$tdclass align=center><input type=button value=\"редакт.\" class=button onclick=\"mod_news_action('edit'," . $row['id'] . ")\"></td>";
			if($this->prms['UseComments']->Value)$retVal.="<td class=$tdclass align=center><input class=button type=button value=\"комментарии\" onclick=\"mod_news_action('comments'," . $row['id'] . ")\"></td>";
			if($this->prms['mailtoclients']->Value)$retVal.="<td class=$tdclass align=center><input class=button type=button value=\"разослать\" onclick=\"mod_news_action('sendtoclients'," . $row['id'] . ")\"></td>";
			$retVal.="</tr>";
			$tdclass=($tdclass=='data1')?'data2':'data1';
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
				$PagerStr.="<a href=\"javascript:mod_news_action('',0,$fcounter)\">$fcounter</a> ";
			};
		};
		$PagerStr.="</td></tr>" . drwTableEnd();
	};
	$retVal.=$PagerStr;
	return $retVal;
    }

    function MakeAdminEditorScreen($theNode,$theFormPrefix,$theID, $thePage){
	$retVal='';
	$sql="select `id`, `node`, `author`, `subject`, `writedate`, `rtext`, `shortrtext`, `visible`, `deleted`, `showrtext` from `" . $this->newsTable . "` where `deleted`<>1 and `node`=$theNode and `id`=$theID";
	if(!$this->dbc->sql_query($sql)){
	    $sqlerror=$this->dbc->sql_error();
	    die($sqlerror['message']);
	};
	if($this->dbc->sql_numrows()==0){
		$retVal='Такой новости не существует';
		return retVal;
	};
	$row=$this->dbc->sql_fetchrow();
	$wdate=date("d.m.Y",$row['writedate']);
	$retVal.=drwTableBegin('100%','') . "<tr><td class=colheader colspan=2>редактирование новости<br><a class=header href=\"javascript:mod_news_action('',0)\">[вернуться к списку новостей]</a></td></tr>" . $theFormPrefix;
	$retVal.="<input type=hidden name=mod_action value=update><input type=hidden name=id value=$theID><input type=hidden name=page value=$thePage>";
	$retVal.="<tr><td class=data1 align=right>Тема:</td><td class=data1 align=left><input type=text class=text name=subject value=\"" . CutQuots($row['subject']) . "\" size=60 maxlength=250></td></tr>";
	$retVal.="<tr><td class=data2 align=right>Дата публикации:</td><td class=data2 align=left>" . DatePicker('WriteDate',$row['writedate']) . "</td></tr>";
	$retVal.="<tr><td class=data1 align=right>Автор:</td><td class=data1 align=left><input type=text class=text name=Author value=\"" . CutQuots($row['author']) . "\" size=60 maxlength=250 readonly></td></tr>";
	$visible=($row['visible']==1)?' checked':'';
	$retVal.="<tr><td class=data2 align=right>Доступность на сайте:</td><td class=data2 align=left><input type=checkbox name=Visible$visible></td></tr>";
	$retVal.="<tr><td class=data1 align=center colspan=2>Краткий текст новости:</td></tr><tr><td class=data1 colspan=2>";
	$retVal.="<iframe name=\"mod_news_editor1\" border=0 width=100% height=300 marginwidth=0 marginheight=0 scrolling=yes frameborder=0></iframe>";
	$retVal.="</td></tr>";
	$ShowrText=($row['showrtext']==1)?' checked':'';
	$retVal.="<tr><td class=data2 align=right>Использовать полный текст:</td><td class=data2 align=left><input type=checkbox name=ShowrText$ShowrText></td></tr>";

	$retVal.="<tr><td class=data1 align=center colspan=2>Полный текст новости:</td></tr><tr><td class=data1 colspan=2>";
	$retVal.="<iframe name=\"mod_news_editor2\" border=0 width=100% height=400 marginwidth=0 marginheight=0 scrolling=yes frameborder=0></iframe>";
	$retVal.="</td></tr>";

	$retVal.="<tr><td colspan=2 class=data2 align=center><input type=checkbox name=delete> - удалить&nbsp;&nbsp;&nbsp;<input type=submit class=button value=\"обновить\"></td></tr>";
	$retVal.="</form>" . drwTableEnd();

	$retVal.="<form method=post action=post.php enctype=\"multipart/form-data\" name=mod_news_go_form1 target=mod_news_editor1><input type=hidden name=textID value=" . $row['shortrtext'] . "></form>";
	$retVal.="<form method=post action=post.php enctype=\"multipart/form-data\" name=mod_news_go_form2 target=mod_news_editor2><input type=hidden name=textID value=" . $row['rtext'] . "></form>";
	$retVal.="<script>document.forms['mod_news_go_form1'].submit();document.forms['mod_news_go_form2'].submit();</script>";

	return $retVal;

    }

    function MakeAdminCommentsScreen($theNode,$theFormPrefix,$theID, $thePage){
	$retVal='';
	$sql="select `id`, `node`, `author`, `subject`, `writedate`, `rtext`, `shortrtext`, `visible`, `deleted`, `showrtext` from `" . $this->newsTable . "` where `deleted`<>1 and `node`=$theNode and `id`=$theID";
	if(!$this->dbc->sql_query($sql)){
	    $sqlerror=$this->dbc->sql_error();
	    die($sqlerror['message']);
	};
	if($this->dbc->sql_numrows()==0){
		$retVal='Такой новости не существует';
		return $retVal;
	};
	$row->$this->dbc->sql_fetchrow();
	$wdate=date("d.m.Y",$row['writedate']);
	$retVal.=drwTableBegin('100%','') . "<tr><td class=colheader colspan=2>модерирование комментариев к новости \"" . CutQuots($row['subject']) . "\" от [$wdate]<br><a href=\"javascript:mod_news_action('',0)\">[вернуться к списку новостей]</a></td></tr>";
	$sql="select `id`, `nid`, `writedate`, `author`, `email`, `comment`, `deleted`, `remoteip` from `" . $this->commentsTable . "` where `deleted`<>1 and `nid`=$theID order by `writedate`";
	if(!$this->dbc->sql_query($sql)){
	    $sqlerror=$this->dbc->sql_error();
	    die($sqlerror['message']);
	};
	$tdclass='data1';
	while($row=$this->dbc->sql_fetchrow()){
		$wdate=date("d.m.Y H:i",$row['writedate']);
		$Author=$row['author'];
		$Email=(strlen($row['email'])>0)?', e-mail: <a href="mailto:' . $row['email'] . '">' . $row['email'] . '</a>':'';
		$Comment=(strlen($row['comment'])>0)?$row['comment']:'';
		$Comment=CutQuots($Comment);
		$Comment=preg_replace("/(\r)?\n/","<br>",$Comment);
		$retVal.="<tr><td class=$tdclass><strong>[$wdate] " . CutQuots($Author) . "</strong>$Email, IP-адрес: " . $row['remoteip'];
		$retVal.="<br>$Comment";
		$retVal.="<br><input type=button class=button value=\"удалить\" onclick=\"mod_news_action('comments_delete',$theID,$thePage," . $row['id'] . ")\"></td></tr>";
		$tdclass=($tdclass=='data1')?'data2':'data1';
	};
	$retVal.=drwTableEnd();
	return $retVal;
    }


    function MakeUserOuput($theNode, $theFormPrefix){
	global $HTTP_POST_VARS, $HTTP_GET_VARS, $SAmodsArray;
	$retArray=array();
	$retVal="";
	$retVal2=$this->prms["MinislotTemplate"]->Value;
	$PageNum=$HTTP_GET_VARS['page'];
	$PageNum=($PageNum>0)?$PageNum:1;
	$needdate=$HTTP_GET_VARS['date'];
	$NewsID=$HTTP_GET_VARS['n'];
	if($this->prms["mailtoclients"]->Value){
		$ssAction=$SAmodsArray["subscribe"]->MakeUserOuput($theNode, $theFormPrefix);
		if(strlen($ssAction)>0)return array($ssAction,"");
	};
	if($this->prms["mailtoclients"]->Value){
		$ssForm=$SAmodsArray["subscribe"]->SubscribeForm($theNode, $theFormPrefix);
	}else{
		$ssForm="";
	};
	if($NewsID>0){
		$retVal=$this->MakeUserItemShow($theNode, $theFormPrefix, $NewsID, $PageNum);
	}else{
		if($needdate!=''){
			$itemslist=$this->MakeUserListingPerDate($theNode, $theFormPrefix, $needdate);
			$pageslist='';
		}else{
			$itemslist=$this->MakeUserListing($theNode, $theFormPrefix, $PageNum);
			$pageslist=$this->MakePageChanger($theNode, $theFormPrefix, $PageNum);
		};
		$retVal=$this->prms['PageTemplate']->Value;
		$retVal=str_replace("--itemslist--",$itemslist,$retVal);
		$retVal=str_replace("--pager--",$pageslist,$retVal);
		$retVal=str_replace("--subscribeform--",$ssForm,$retVal);
		$retVal=str_replace("--dateselector--",$this->MakeDateSelect($theNode, $theFormPrefix),$retVal);
	};
	$retVal2=str_replace("--subscribeform--",$ssForm,$retVal2);
	$retVal2=str_replace("--calendar--",$this->MakeCalendar($theNode, $theFormPrefix),$retVal2);
	$retVal2=str_replace("--dateselector--",$this->MakeDateSelect($theNode, $theFormPrefix),$retVal2);
	$retVal.=$this->UserFinalOutput;
	$retArray[0]=$retVal;
	$retArray[1]=$retVal2;
	return $retArray;
    }

    function MakeUserListingPerDate($theNode, $theFormPrefix, $theDate){
	$retVal='';
	$FirstPageD=$this->prms['FirstPageDays']->Value;
	$PageSize=$this->prms['PageSize']->Value;
	$ItemTemplate=$this->prms['ItemTemplate']->Value;
	$Devider=$this->prms['ItemsDevider']->Value;
	if(strlen($theDate)!=8)return "";

	$monthbegin=mktime(0,0,0,substr($theDate,4,2),0,substr($theDate,0,4));
	$monthend=mktime(0,0,0,substr($theDate,4,2)+1,0,substr($theDate,0,4));
	$starttime=mktime(0,0,0,substr($theDate,4,2),substr($theDate,6,2),substr($theDate,0,4));
	$finishtime=$starttime+(3600*24);
	$sql="select `writedate` from `" . $this->newsTable . "` where `writedate`<$finishtime and `writedate`>=$monthbegin and `visible`=1 and `deleted`<>1 order by `writedate` desc";
	$this->dbc->sql_query($sql);
	if($this->dbc->sql_numrows()==0){
		$sql="select `writedate` from `" . $this->newsTable . "` where `writedate`>$finishtime and `visible`=1 and `deleted`<>1 order by `writedate`";
		$this->dbc->sql_query($sql);
		if($this->dbc->sql_numrows()==0){
			$sql="select `writedate` from `" . $this->newsTable . "` where `writedate`<$finishtime and `visible`=1 and `deleted`<>1 order by `writedate` desc";
			$this->dbc->sql_query($sql);
			if($this->dbc->sql_numrows()==0){
				return "";
			};
		};
	};
	if(!$row=$this->dbc->sql_fetchrow()){
		return "";
	};
	$needntime=$row['writedate'];
        
	$starttime=mktime(0,0,0,date("n",$needntime),date("j",$needntime),date("Y",$needntime));
	$finishtime=$starttime+(3600*24);
	$this->CurUserDate=$starttime;
	$sql="select `nt`.`id` as `id`, `nt`.`node` as `node`, `nt`.`author` as `author`, `nt`.`subject` as `subject`, `nt`.`writedate` as `writedate`, `ft`.`text` as `text`,  `nt`.`showrtext` as `showrtext`, `st`.`text` as `shorttext`, `nt`.`visible` as `visible`, `nt`.`deleted` as `deleted`, count(`ct`.`id`) as `commentscount`";
	$sql.=" from `" . $this->newsTable . "` as `nt` left join `" . $this->commentsTable . "` as `ct` on `ct`.`nid`=`nt`.`id`, `texts` as `ft`, `texts` as `st` where `nt`.`deleted`<>1 and `nt`.`visible`=1 and `ft`.`id` = `nt`.`rtext` and `nt`.`shortrtext`=`st`.`id` and `nt`.`node`=$theNode and `nt`.`writedate`>=$starttime and `nt`.`writedate`<$finishtime";
	$sql.=" group by `nt`.`id`, `nt`.`node`, `nt`.`author`, `nt`.`subject`, `nt`.`writedate`, `ft`.`text`,  `nt`.`showrtext`, `st`.`text`, `nt`.`visible`, `nt`.`deleted`";
	$sql.=" order by `nt`.`writedate` desc";
	if(!$this->dbc->sql_query($sql)){
	    $sqlerror=$this->dbc->sql_error();
	    die($sqlerror['message']);
	};
	while($row=$this->dbc->sql_fetchrow()){
			$OneItem=$ItemTemplate;
			if($row['showrtext']==1){
				$LinkToFull=$this->prms['ItemLinkFullTemplate']->Value;
				$LinkToFull=str_replace("--subject--",CutQuots($row['subject']),$LinkToFull);
				$LinkToFull=str_replace("--striptext--",striphtml($row['shorttext']),$LinkToFull);
				$LinkToFull=str_replace("--text--",$row['shorttext'],$LinkToFull);
				$LinkToFull=str_replace("--href--",$theFormPrefix . '&n=' . $row['id'] . '&page=' . $thePage, $LinkToFull);
			}else{
				$LinkToFull='';
			};

			if($this->prms['UseComments']->Value){
				$LinkToComments=$this->prms['ItemLinkCommentsTemplate']->Value;
				$LinkToComments=str_replace("--subject--",CutQuots($row['subject']),$LinkToComments);
				$LinkToComments=str_replace("--href--",$theFormPrefix . '&n=' . $row['id'] . '&page=' . $thePage, $LinkToComments);
				$LinkToComments=str_replace("--count--",$row['commentscount'], $LinkToComments);
			}else{
				$LinkToComments='';
			};

			$OneItem=str_replace("--date--",date($this->prms['DateFormat']->Value,$row['writedate']),$OneItem);
			$OneItem=str_replace("--subject--",CutQuots($row['subject']),$OneItem);
			$OneItem=str_replace("--text--",$row['shorttext'],$OneItem);
			$OneItem=str_replace("--striptext--",striphtml($row['shorttext']),$OneItem);
			$OneItem=str_replace("--author--",CutQuots($row['author']),$OneItem);
			$OneItem=str_replace("--linkfull--",$LinkToFull,$OneItem);
			$OneItem=str_replace("--hreffull--",$theFormPrefix . '&n=' . $row['id'] . '&page=' . $thePage,$OneItem);
			$OneItem=str_replace("--linkcomments--",$LinkToComments,$OneItem);
			$retVal.=$OneItem . $Devider;
	};
	if(strlen($retVal)>strlen($Devider))$retVal=substr($retVal,0,(strlen($retVal)-strlen($Devider)));
	$this->ListingSize=$shifted;
	return $retVal;
    }

    function MakeUserListing($theNode, $theFormPrefix, $thePage){
	$retVal='';
	$FirstPageD=$this->prms['FirstPageDays']->Value;
	$PageSize=$this->prms['PageSize']->Value;
	$ItemTemplate=$this->prms['ItemTemplate']->Value;
	$Devider=$this->prms['ItemsDevider']->Value;
	$PageNum=$thePage;
	if($FirstPageD>0){
		$sd = time()-($FirstPageD*3600*24);
		$sql="select count(`id`) as `nc` from `" . $this->newsTable . "` where `deleted`<>1 and `visible`=1 and `node`=" . $theNode . " and `writedate`>=" . $sd;
		if(!$this->dbc->sql_query($sql)){
		    $sqlerror=$this->dbc->sql_error();
		    die($sqlerror['message']);
		};
		$RealFirstPageSize=$this->dbc->sql_fetchfield('nc');
		if($RealFirstPageSize>0){
			$FirstShifting=$RealFirstPageSize-$PageSize;
		}else{
			$FirstShifting=0;
		};
	}else{
		$FirstShifting=0;
	};

	$sql="select `nt`.`id` as `id`, `nt`.`node` as `node`, `nt`.`author` as `author`, `nt`.`subject` as `subject`, `nt`.`writedate` as `writedate`, `ft`.`text` as `text`,  `nt`.`showrtext` as `showrtext`, `st`.`text` as `shorttext`, `nt`.`visible` as `visible`, `nt`.`deleted` as `deleted`, count(`ct`.`id`) as `commentscount`";
	$sql.=" from `" . $this->newsTable . "` as `nt` left join `" . $this->commentsTable . "` as `ct` on `ct`.`nid`=`nt`.`id`, `texts` as `ft`, `texts` as `st` where `nt`.`deleted`<>1 and `nt`.`visible`=1 and `ft`.`id` = `nt`.`rtext` and `nt`.`shortrtext`=`st`.`id` and `nt`.`node`='$theNode'";
	$sql.=" group by `nt`.`id`, `nt`.`node`, `nt`.`author`, `nt`.`subject`, `nt`.`writedate`, `ft`.`text`,  `nt`.`showrtext`, `st`.`text`, `nt`.`visible`, `nt`.`deleted`";
	$sql.=" order by `nt`.`writedate` desc";
	if(!$this->dbc->sql_query($sql)){
	    $sqlerror=$this->dbc->sql_error();
	    die($sqlerror['message']);
	};
	$counter=0;
	$shifted=0;
	$OneItem='';
	$LinkToFull='';
	$LinkToComments='';
	$this->CurUserDate=0;
	while($row=$this->dbc->sql_fetchrow()){
		$counter++;
		$shifted=$counter-$FirstShifting;
		$InPage=(($shifted>(($PageNum-1)*$PageSize))&&($shifted<=(($PageNum)*$PageSize)));
		$InPage=$InPage||(($PageNum==1)&&($shifted<=0));
		if($InPage){
			if($this->CurUserDate==0)$this->CurUserDate=$row['writedate'];
			$OneItem=$ItemTemplate;
			if($row["showrtext"]==1){
				$LinkToFull=$this->prms['ItemLinkFullTemplate']->Value;
				$LinkToFull=str_replace("--subject--",CutQuots($row['subject']),$LinkToFull);
				$LinkToFull=str_replace("--striptext--",striphtml($row['shorttext']),$LinkToFull);
				$LinkToFull=str_replace("--text--",$row['shorttext'],$LinkToFull);
				$LinkToFull=str_replace("--href--",$theFormPrefix . '&n=' . $row['id'] . '&page=' . $thePage, $LinkToFull);
			}else{
				$LinkToFull='';
			};

			if($this->prms['UseComments']->Value){
				$LinkToComments=$this->prms['ItemLinkCommentsTemplate']->Value;
				$LinkToComments=str_replace("--subject--",CutQuots($row['subject']),$LinkToComments);
				$LinkToComments=str_replace("--href--",$theFormPrefix . '&n=' . $row['id'] . '&page=' . $thePage, $LinkToComments);
				$LinkToComments=str_replace("--count--",$row['commentscount'], $LinkToComments);
			}else{
				$LinkToComments='';
			};

			$OneItem=str_replace("--date--",date($this->prms['DateFormat']->Value,$row['writedate']),$OneItem);
			$OneItem=str_replace("--subject--",CutQuots($row['subject']),$OneItem);
			$OneItem=str_replace("--text--",$row['shorttext'],$OneItem);
			$OneItem=str_replace("--striptext--",striphtml($row['shorttext']),$OneItem);
			$OneItem=str_replace("--author--",CutQuots($row['author']),$OneItem);
			$OneItem=str_replace("--linkfull--",$LinkToFull,$OneItem);
			$OneItem=str_replace("--hreffull--",$theFormPrefix . '&n=' . $row['id'] . '&page=' . $thePage,$OneItem);
			$OneItem=str_replace("--linkcomments--",$LinkToComments,$OneItem);
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
	$PageSize=$this->prms['PageSize']->Value;
	$PageNum=$thePage;
	$lastPage=($counter%$PageSize==0)?0:1;
	$PageHref='';
	$PageLink='';
	$Devider=$this->prms['plDevider']->Value;
	$lastPage+=($counter/$PageSize);
	if($lastPage<2)return "";
	for($fcounter=1;$fcounter<=$lastPage;$fcounter++){
		$PageHref=$theFormPrefix . '&page=' . $fcounter;
		$PageLink=($fcounter==$PageNum)?$this->prms['plActiveTemplate']->Value:$this->prms['plInactiveTemplate']->Value;
		$PageLink=str_replace("--link--",$PageHref,$PageLink);
		$PageLink=str_replace("--pagenum--",$fcounter,$PageLink);
		$retVal.=$PageLink . $Devider;
	};
	if(strlen($retVal)>strlen($Devider))$retVal=substr($retVal,0,(strlen($retVal)-strlen($Devider)));
	if(strlen($retVal)>0)$retVal=str_replace("--pageslist--",$retVal,$this->prms['plTemplate']->Value);
	return $retVal;
    }


    function MakeCalendar($theNode, $theFormPrefix){
    	global $month;
    	$abledates=array();
    	$tmpl=$this->prms['CalendarTemplate']->Value;
    	$retVal='';
	if($this->CurUserDate==0)$this->CurUserDate=time();
	$dateArray=getdate($this->CurUserDate);
	$curOnlyDate=mktime(0,0,0,$dateArray['mon'],$dateArray['mday'],$dateArray['year']);
        $sql="select `writedate` from `" .$this->newsTable . "` where `deleted`<>1 and `visible`=1 and `node`='$theNode' order by `writedate`";
	if(!$this->dbc->sql_query($sql)){
	    $sqlerror=$this->dbc->sql_error();
	    die($sqlerror['message']);
	};
	while($row=$this->dbc->sql_fetchrow()){
		$newsdatearay=getdate($row['writedate']);
		$onlydate=mktime(0,0,0,$newsdatearay['mon'],$newsdatearay['mday'],$newsdatearay['year']);
		$abledates['d' . $onlydate]=1;
	};
	$abegin=mktime(0,0,0,$dateArray['mon'],1,$dateArray['year']);
	$aend=mktime(0,0,0,$dateArray['mon']+1,0,$dateArray['year']);
	$fdshift=date("w",$abegin)-1;
	if($fdshift==-1)$fdshift=6;
	$monprev=mktime(0,0,0,$dateArray['mon'],0,$dateArray['year']);
	$monnext=mktime(0,0,0,$dateArray['mon']+1,1,$dateArray['year']);
	$retVal=$tmpl;
	$retVal=str_replace("--curmon--",$month[$dateArray['mon']] . ", " . $dateArray['year'] . " г.",$retVal);
	$retVal=str_replace("--monprev--",$theFormPrefix . "&date=" . date("Ymd",$monprev),$retVal);
	$retVal=str_replace("--monnext--",$theFormPrefix . "&date=" . date("Ymd",$monnext),$retVal);
	$lines='<tr>';
	for($counter=1;$counter<=$fdshift;$counter++){
		$lines.="<td></td>";
	};
	$wdcount=0;
	for($counter=$abegin;$counter<=$aend;$counter+=(24*3600)){
		$wdcount++;
		if(($wdcount+$fdshift-1)%7==0)$lines.="</tr><tr>";
		if($abledates['d' . $counter]==1){
			if($curOnlyDate==$counter){
				$ctd=$this->prms['CalendarDateCurrent']->Value;
			}else{
				$ctd=$this->prms['CalendarDateLink']->Value;
			};
		}else{
			$ctd=$this->prms['CalendarDate']->Value;
		};
		$ctd=str_replace("--link--",$theFormPrefix . "&date=" . date("Ymd",$counter),$ctd);
		$ctd=str_replace("--mdate--", date("d",$counter),$ctd);
		$lines.=$ctd;
	};
	$retVal=str_replace("--dates--",$lines,$retVal);
	return $retVal;
    }

    function MakeDateSelect($theNode, $theFormPrefix){
    	global $month;
    	$retVal=$this->prms['DateSelectTemplate']->Value;
	if($this->CurUserDate==0)$this->CurUserDate=time();
	$dateArray=getdate($this->CurUserDate);
	$dayselect="<select name=day>";
	for($aday=1;$aday<=31;$aday++){
		$selected=($aday==$dateArray["mday"])?" selected":"";
		$optval=($aday<10)?"0" . $aday:$aday;
		$dayselect.="<option $selected value=$optval>$aday";
	};
	$dayselect.="</select>";
	$monselect="<select name=month>";
	for($amonth=1;$amonth<=12;$amonth++){
		$selected=($amonth==$dateArray["mon"])?" selected":"";
		$optval=($amonth<10)?"0" . $amonth:$amonth;
		$monselect.="<option $selected value=$optval>" . $month[$amonth];
	};
	$monselect.="</select>";
	$yearselect="<select name=year>";
	$curdate=getdate(time());
	$sql="select max(`writedate`) as `md` from `" . $this->newsTable . "`";
	$this->dbc->sql_query($sql);
	$maxdate=getdate($this->dbc->sql_fetchfield('md'));
	$finy=($maxdate["year"]>0)?$maxdate["year"]:$curdate["year"]+1;

	$sql="select min(`writedate`) as `md` from `" . $this->newsTable . "`";
	$this->dbc->sql_query($sql);
	$mindate=getdate($this->dbc->sql_fetchfield('md'));
	$starty=($mindate["year"]>0)?$mindate["year"]:$curdate["year"]-5;
	for($ayear=$starty;$ayear<=$finy;$ayear++){
		$selected=($ayear==$dateArray["year"])?" selected":"";
		$yearselect.="<option $selected value=$ayear>$ayear";
	};
	$yearselect.="</select>";
	$retVal=str_replace("--day--",$dayselect,$retVal);
	$retVal=str_replace("--month--",$monselect,$retVal);
	$retVal=str_replace("--year--",$yearselect,$retVal);
	$retVal=str_replace("--formname--","mod_news_date_select_form",$retVal);
	$retVal=str_replace("--golink--","javascript:news_dateselector()",$retVal);
	$retVal="<script>function news_dateselector(){self.location='$theFormPrefix&date='+document.forms['mod_news_date_select_form'].year.value+document.forms['mod_news_date_select_form'].month.value+document.forms['mod_news_date_select_form'].day.value;};</script>" . $retVal;
	return $retVal;
    }

    function MakeUserCommentsListing($theNode, $theFormPrefix, $theNID){
	$retVal='';
	$OneItem='';
	$ItemTemplate=$this->prms['CommentsItemTemplate']->Value;
	$Devider=$this->prms['CommentsItemsDevider']->Value;
	$sql="select `id`, `nid`, `writedate`, `author`, `email`, `comment`, `deleted` from `" . $this->commentsTable . "` where `deleted`<>1 and `nid`='$theNID' order by `writedate`";
	if(!$this->dbc->sql_query($sql)){
	    $sqlerror=$this->dbc->sql_error();
	    die($sqlerror['message']);
	};
	while($row=$this->dbc->sql_fetchrow()){
		$OneItem=$ItemTemplate;
		$theComment=$row['comment'];
		$theComment=preg_replace("/(\r)*\n/","<br>",$theComment);
		$OneItem=str_replace("--author--",CutQuots($row['author']),$OneItem);
		$OneItem=str_replace("--email--",$row['email'],$OneItem);
		$OneItem=str_replace("--comment--",InsertReferences($theComment),$OneItem);
		$OneItem=str_replace("--date--",date($this->prms['DateFormat']->Value,$row['writedate']),$OneItem);
		$retVal.=$OneItem . $Devider;
	};
	if(strlen($retVal)>strlen($Devider))$retVal=substr($retVal,0,(strlen($retVal)-strlen($Devider)));
	return $retVal;
    }

    function MakeUserItemShow ($theNode, $theFormPrefix, $theNID, $thePage){
	global $client_ip, $HTTP_POST_VARS;
	$retVal='';
	$Comments='';
	if($this->prms['UseComments']->Value){
		if($HTTP_POST_VARS['action']=='comment'){
			$afb=time()-$this->prms['AntiFloodTO']->Value;
			$sql="select `id`, `author`,`writedate` from `" . $this->commentsTable . "` where `nid`=$theNID and `remoteip`='$client_ip' and `deleted`<>1 and `writedate`>$afb";
			if(!$this->dbc->sql_query($sql)){
			    $sqlerror=$this->dbc->sql_error();
			    die($sqlerror['message']);
			};
			if($this->dbc->sql_numrows()!=0){
				$this->UserFinalOutput.="<script>alert('" . CutQuots($this->prms['AntiFloodMessage']->Value) . "')</script>";
			}else{
				$author=$HTTP_POST_VARS['author'];
				$email=$HTTP_POST_VARS['email'];
				$comment=$HTTP_POST_VARS['comment'];
				$sql="insert into `" . $this->commentsTable . "` (`nid`, `writedate`, `author`, `email`, `comment`, `deleted`, `remoteip`) values ('$theNID'," . time() . ",'$author','$email','$comment',0,'$client_ip')";
				if(strlen($comment)>0){
				    if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror['message']);
				    };
				};
				header ("Location: $theFormPrefix&n=$theNID&page=$thePage&written");
				return;
			};
		};
		$CommentsList=$this->MakeUserCommentsListing($theNode, $theFormPrefix, $theNID);
		$CommentsForm=$this->prms['CommentsFormTemplate']->Value;
		$CommentsForm=str_replace("--form--", "<form method=post action=\"$theFormPrefix&n=$theNID&page=$thePage\">",$CommentsForm);
		$Comments=$this->prms['CommentsTemplate']->Value;
		$Comments=str_replace("--hreflist--",$theFormPrefix . '&page=' . $thePage, $Comments);
		$Comments=str_replace("--itemslist--",$CommentsList, $Comments);
		$Comments=str_replace("--commentform--",$CommentsForm, $Comments);
	};

	$sql="select `nt`.`id` as `id`, `nt`.`node` as `node`, `nt`.`author` as `author`, `nt`.`subject` as `subject`, `nt`.`writedate` as `writedate`, `ft`.`text` as `text`,  `nt`.`showrtext` as `showrtext`, `st`.`text` as `shorttext`, `nt`.`visible` as `visible`, `nt`.`deleted` as `deleted`, count(`ct`.`id`) as `commentscount`";
	$sql.=" from `" . $this->newsTable . "` as `nt` left join `" . $this->commentsTable . "` as `ct` on `ct`.`nid`=`nt`.`id`, `texts` as `ft`, `texts` as `st` where `nt`.`deleted`<>1 and `nt`.`visible`=1 and `ft`.`id` = `nt`.`rtext` and `nt`.`shortrtext`=`st`.`id` and `nt`.`node`='$theNode'  and `nt`.`id`='$theNID'";
	$sql.=" group by `nt`.`id`, `nt`.`node`, `nt`.`author`, `nt`.`subject`, `nt`.`writedate`, `ft`.`text`,  `nt`.`showrtext`, `st`.`text`, `nt`.`visible`, `nt`.`deleted`";
	if(!$this->dbc->sql_query($sql)){
	    $sqlerror=$this->dbc->sql_error();
	    die($sqlerror['message']);
	};
	if($row=$this->dbc->sql_fetchrow()){
		$this->CurUserDate=$row['writedate'];
		$retVal=$this->prms['ItemFullTemplate']->Value;
		$retVal=str_replace("--date--",date($this->prms['DateFormat']->Value,$row['writedate']),$retVal);
		$retVal=str_replace("--subject--",CutQuots($row['subject']),$retVal);
		$thetext=($row['showrtext']==1)?$row['text']:$row['shorttext'];
		$retVal=str_replace("--text--",$thetext,$retVal);
		$retVal=str_replace("--author--",CutQuots($row['author']),$retVal);
		$retVal=str_replace("--comments--",$Comments,$retVal);
		$retVal=str_replace("--hreflist--",$theFormPrefix . '&page=' . $thePage,$retVal);
	};
	return $retVal;
    }

    function SearchString($theText){
	$retVal=array();
	$sql="select `nt`.`id` as `id`, `nt`.`node` as `node`, `nt`.`author` as `author`, `nt`.`subject` as `subject`, `nt`.`writedate` as `writedate`, `ft`.`text` as `text`,  `nt`.`showrtext` as `showrtext`, `st`.`text` as `shorttext`, `nt`.`visible` as `visible`, `nt`.`deleted` as `deleted`";
	$sql.=" from `" . $this->newsTable . "` as `nt`, `texts` as `ft`, `texts` as `st` where `nt`.`deleted`<>1 and `nt`.`visible`=1 and `ft`.`id` = `nt`.`rtext` and `nt`.`shortrtext`=`st`.`id` and (UPPER(`st`.`text`) like UPPER('%" . str_replace("'","''",$theText) . "%') or UPPER(`ft`.`text`) like UPPER('%" . str_replace("'","''",$theText) . "%'))";
	$sql.=" order by `nt`.`writedate` desc";
	if(!$this->dbc->sql_query($sql)){
		$sqlerror=$this->dbc->sql_error();
		die($sqlerror['message']);
	};
	while($row=$this->dbc->sql_fetchrow()){
	  	$foundText=striphtml($row['text']);
	    	$charpos=strpos(strtoupper($foundText),strtoupper($theText));
	  	if(($charpos)&&($row['showrtext'])){
	  		$foundText=$foundText;
	  	}else{
		  	$foundText=striphtml($row['shorttext']);
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
		$retVal[$counter]->QSParams='&n=' . $row['id'];
		$counter++;
	};
	return $retVal;
    }

	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->newsTable`;
			CREATE TABLE `$this->newsTable` (
			  `id` int(11) NOT NULL auto_increment,
			  `node` int(11) NOT NULL default '0',
			  `writedate` int(11) NOT NULL default '0',
			  `subject` varchar(250) NOT NULL default '',
			  `author` varchar(250) NOT NULL default '',
			  `shortrtext` int(11) NOT NULL default '0',
			  `rtext` int(11) NOT NULL default '0',
			  `visible` int(11) NOT NULL default '0',
			  `deleted` int(11) NOT NULL default '0',
			  `showrtext` int(11) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			);
			DROP TABLE IF EXISTS `$this->commentsTable`;
			CREATE TABLE `$this->commentsTable` (
			  `id` int(11) NOT NULL auto_increment,
			  `nid` int(11) NOT NULL default '0',
			  `writedate` int(11) NOT NULL default '0',
			  `author` varchar(250) NOT NULL default '',
			  `email` varchar(250) NOT NULL default '',
			  `comment` longtext NOT NULL,
			  `deleted` int(11) NOT NULL default '0',
			  `remoteip` varchar(20) NOT NULL default '',
			  PRIMARY KEY  (`id`)
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


$theNewsModule=new clsNewsModule('news','новости',$db);
$modsArray['news']=$theNewsModule;

?>