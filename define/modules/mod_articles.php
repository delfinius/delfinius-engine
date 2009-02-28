<?php

class clsArticlesModule extends clsModule{


	function clsArticlesModule($modName,$modDName,$dbconnector){
	 	parent::clsModule($modName,$modDName,$dbconnector);
	 	$this->SearchAble=true;
		$this->version="1.0.3";
		$this->helpstring='<p>Модуль предназначен для публикации статей, файлов, шаблонных документов.</p>';
		$this->prms['AdminPageSize']=new ConfigParam('AdminPageSize');
		$this->prms['AdminPageSize']->Description='Количество отображаемых публикаций на странице модератора.';
		$this->prms['AdminPageSize']->DataType='int';
		$this->prms['AdminPageSize']->Value=50;
		$this->prms['AdminPageSize']->Protected=true;

		$this->prms['PageSize']=new ConfigParam('PageSize');
		$this->prms['PageSize']->Description='Количество отображаемых публикация на одной странице.';
		$this->prms['PageSize']->DataType='int';
		$this->prms['PageSize']->Value=30;
		$this->prms['PageSize']->Protected=false;

		$this->prms['PageTemplate']=new ConfigParam('PageTemplate');
		$this->prms['PageTemplate']->Description="Шаблон вывода списка опубликованных документов. Допускаемые для замены значения - preamble, itemslist, pager.";
		$this->prms['PageTemplate']->DataType='memo';
		$this->prms['PageTemplate']->Value="--preamble--<table width=100% border=0 cellpadding=2 cellspacing=0>--itemslist--</table>--pager--";

		$this->prms['ItemTemplate']=new ConfigParam('ItemTemplate');
		$this->prms['ItemTemplate']->Description="Шаблон отображения одной публикации в списке публикаций внутри шаблона PageTemplate. Допускаемые для замены значения: date, name, href, text";
		$this->prms['ItemTemplate']->DataType='memo';
		$this->prms['ItemTemplate']->Value="<tr><td width=100% class=colheader><a href=\"--href--\">--name--</a></td></tr>";
		$this->prms['ItemTemplate']->Protected=false;

		$this->prms['ItemsDevider']=new ConfigParam('ItemsDevider');
		$this->prms['ItemsDevider']->Description='html-код разделяющий отдельные публикации на странице';
		$this->prms['ItemsDevider']->DataType='memo';
		$this->prms['ItemsDevider']->Value="<tr><td></td><td height=1><img src=format.gif border=0 width=1 height=1></td></tr>";
		$this->prms['ItemsDevider']->Protected=false;

		$this->prms['ItemFullTemplate']=new ConfigParam('ItemFullTemplate');
		$this->prms['ItemFullTemplate']->Description='Шаблон отображения публикации. Допускаемые для замены значения: name, text, hreflist (адрес возвращающий на список публикаций)';
		$this->prms['ItemFullTemplate']->DataType='memo';
		$this->prms['ItemFullTemplate']->Value="<table width=100% border=0 cellpadding=2 cellspacing=0><tr><td class=colheader width=100% >--name--</td></tr><tr><td>--text--</td></tr><tr><td align=right><a href=\"--hreflist--\">назад</a></td></tr></table>";
		$this->prms['ItemFullTemplate']->Protected=false;

		$this->prms['MinislotTemplate']=new ConfigParam('MinislotTemplate');
		$this->prms['MinislotTemplate']->Description="Шаблон вывода списка опубликованных документов для второго слота вывода (список других публикаций раздела показываемый в рядом с просматриваемой публикацией). Допускаемые для замены значения - itemslist.";
		$this->prms['MinislotTemplate']->DataType='memo';
		$this->prms['MinislotTemplate']->Value="";

		$this->prms['MinislotItemTemplate']=new ConfigParam('MinislotItemTemplate');
		$this->prms['MinislotItemTemplate']->Description="Шаблон отображения одной публикации в списке публикаций внутри шаблона MinislotTemplate. Допускаемые для замены значения: date, name, href";
		$this->prms['MinislotItemTemplate']->DataType='memo';
		$this->prms['MinislotItemTemplate']->Value="<a href=\"--href--\" class=menu3>--name--</a>";
		$this->prms['MinislotItemTemplate']->Protected=false;

		$this->prms['MinislotSelectedItemTemplate']=new ConfigParam('MinislotSelectedItemTemplate');
		$this->prms['MinislotSelectedItemTemplate']->Description="Шаблон отображения выбранной публикации в списке публикаций внутри шаблона MinislotTemplate. Допускаемые для замены значения: date, name, href";
		$this->prms['MinislotSelectedItemTemplate']->DataType='memo';
		$this->prms['MinislotSelectedItemTemplate']->Value="<a href=\"--href--\" class=menu3a>--name--</a>";
		$this->prms['MinislotSelectedItemTemplate']->Protected=false;


		$this->prms['MinislotItemsDevider']=new ConfigParam('MinislotItemsDevider');
		$this->prms['MinislotItemsDevider']->Description="html-код разделяющий отдельные публикации на странице MinislotTemplate";
		$this->prms['MinislotItemsDevider']->DataType='memo';
		$this->prms['MinislotItemsDevider']->Value="<br><img src=format.gif border=0 width=1 height=3><br>";
		$this->prms['MinislotItemsDevider']->Protected=false;

    		$this->prms['plTemplate']=new ConfigParam('plTemplate');
		$this->prms['plTemplate']->Description='Шаблон отображения списка страниц новостей. Заменяет pager в шаблоне PageTemplate. Допускаемое для замены значение: pageslist';
		$this->prms['plTemplate']->DataType='memo';
		$this->prms['plTemplate']->Value='&nbsp;&nbsp;&nbsp;страница:&nbsp;--pageslist--';
		$this->prms['plTemplate']->Protected=false;

		$this->prms['plInactiveTemplate']=new ConfigParam('plInactiveTemplate');
		$this->prms['plInactiveTemplate']->Description='Шаблон ссылки на неактивную страницу новостей. Допускаемые для замены значения: link, pagenum';
		$this->prms['plInactiveTemplate']->DataType='char';
		$this->prms['plInactiveTemplate']->Value='<a href="--link--">--pagenum--</a>';
		$this->prms['plInactiveTemplate']->Protected=false;

		$this->prms['plActiveTemplate']=new ConfigParam('plActiveTemplate');
		$this->prms['plActiveTemplate']->Description='Шаблон ссылки на текущую страницу новостей. Допускаемые для замены значения: link, pagenum';
		$this->prms['plActiveTemplate']->DataType='char';
		$this->prms['plActiveTemplate']->Value='<a href="--link--" style="color:red;">--pagenum--</a>';
		$this->prms['plActiveTemplate']->Protected=false;

		$this->prms['plDevider']=new ConfigParam('plDevider');
		$this->prms['plDevider']->Description='Символы-разделители ссылок на страницы';
		$this->prms['plDevider']->DataType='char';
		$this->prms['plDevider']->Value='&nbsp;|&nbsp;';
		$this->prms['plDevider']->Protected=false;

		$this->prms['DateFormat']=new ConfigParam('DateFormat');
		$this->prms['DateFormat']->Description='Формат вывода дат. (http://www.php.net/manual/en/function.date.php)';
		$this->prms['DateFormat']->DataType='char';
		$this->prms['DateFormat']->Value="d.m.Y";
		$this->prms['DateFormat']->Protected=false;

		$this->prefsTable='mod_articles_prefs';
		$this->itemsTable='mod_articles_items';
		$this->ListingSize=0;
	}

	function ClientScript($theNode, $theFormPrefix, $thePage=1){
		$LocalthePage=$thePage;
		$retVal='';
		$ModifedFormPrefix=substr($theFormPrefix,0,strlen($theFormPrefix)-1);
		$ModifedFormPrefix.=' name="mod_articles_action_form">';
		$retVal.=$ModifedFormPrefix . "<input type=hidden name=mod_action value=\"\"><input type=hidden name=id value=0><input type=hidden name=page value=$LocalthePage><input type=hidden name=param1 value=\"\"></form>";
		$retVal.="<script>";
		$retVal.="function mod_articles_action(theAction,theID,thePage,theParam1){";
		$retVal.="document.forms['mod_articles_action_form'].mod_action.value=theAction;\n";
		$retVal.="document.forms['mod_articles_action_form'].id.value=theID;\n";
		$retVal.="if(thePage)document.forms['mod_articles_action_form'].page.value=thePage;";
		$retVal.="if(theParam1)document.forms['mod_articles_action_form'].param1.value=theParam1;";
		$retVal.="document.forms['mod_articles_action_form'].submit();\n";
		$retVal.="};";
		$retVal.="</script>";
		return $retVal;
	}

	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
		global $HTTP_POST_VARS, $HTTP_POST_FILES, $uploadedfilesdir, $doc_root;
		$retVal=drwTableBegin('100%','') . "<tr><td class=header align=center><a class=header href=\"javascript:mod_articles_action('',0)\">публикации раздела</a></td><td class=header align=center><a class=header href=\"javascript:mod_articles_action('editprefs',0)\">параметры раздела</a></td></tr>" . drwTableEnd();
		$mod_future_action='insert';
		$mod_action=$HTTP_POST_VARS['mod_action'];
		$PageNum=$HTTP_POST_VARS['page'];
		$PageNum=($PageNum>0)?$PageNum:1;
		$CurrentID=$HTTP_POST_VARS['id'];
		$retVal.=$this->ClientScript($theNode, $theFormPrefix, $PageNum);
		$CurrentID=($CurrentID>0)?$CurrentID:0;
		if($mod_action=='insert'){
			$type=$HTTP_POST_VARS['type'];
			switch($type){
				case "text":{
					$rText=text_create_new();
					$docid=0;
					break;
				};
				case "file":{
					$rText=0;
					$docid=0;
					break;
				};
				case "doc":{
					$rText=0;
					$docid=0;
					break;
				};
			};
			$Name=(isset($HTTP_POST_VARS['name']))?$HTTP_POST_VARS['name']:'';
			$sql="insert into `" . $this->itemsTable . "` (`node`, `name`, `type`, `rtext`, `filename`, `docid`, `pubdate`, `visible`, `deleted`) values ($theNode, '$Name', '$type', $rText, '', $docid, " . time() . ", 0, 0)";
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

		if($mod_action=="updateprefs"){
			$doctype=$HTTP_POST_VARS['doctype'];
			$doctemplate=$HTTP_POST_VARS['template'];
			$sql="update `" . $this->prefsTable . "` set `doctype`='$doctype' , `template`=$doctemplate where `node`=" . $theNode;
			$this->dbc->sql_query($sql);
			$mod_action="editprefs";
		};

		if($mod_action=="editprefs"){
			$mod_future_action="updateprefs";
			$sql="select `rtext`,`doctype`, `template` from `" . $this->prefsTable . "` where `node`=" . $theNode;
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror['message']);
			};
			if($row=$this->dbc->sql_fetchrow()){
				$doctype=$row["doctype"];
			}else{
				return "";
			};
			$selfile=($doctype=="file")?" selected":"";
			$seltext=($doctype=="text")?" selected":"";
			$retVal.=drwTableBegin('100%','') . "<tr><td class=colheader colspan=2>изменить общие параметры раздела с публикациями</td></tr>" . $theFormPrefix . "<input type=hidden name=mod_action value=\"" . $mod_future_action . "\">";
			$retVal.="<tr><td class=data2 align=right>Тип хранимых документов:</td><td class=data2 align=left><select name=doctype><option value=text$seltext>текст<option value=file$selfile>файл</select></td></tr>";
			$retVal.="<tr><td class=data1 align=right>Шаблон для шаблонных документов:</td><td class=data1 align=left><select name=template><option value=0>(не используется)</select></td></tr>";

			$retVal.="<tr><td class=data2 align=center colspan=2>Вступительный текст раздела:</td></tr><tr><td class=data2 colspan=2>";
			$retVal.="<iframe name=\"mod_articles_editor\" border=0 width=100% height=700 marginwidth=0 marginheight=0 scrolling=yes frameborder=0></iframe>";
			$retVal.="</td></tr>";
			$formsuffix="<form method=post action=post.php enctype=\"multipart/form-data\" name=mod_articles_go_form target=mod_articles_editor><input type=hidden name=textID value=" . $row['rtext'] . "></form>";
			$formsuffix.="<script>document.forms['mod_articles_go_form'].submit();</script>";

			$retVal.="<tr><td class=data2 align=center colspan=2><input type=submit class=button value=\"обновить\"></td></tr>";
			$retVal.="</form>" . $formsuffix . drwTableEnd();
		};

		if($mod_action=='update'){
			$CurrentID=$HTTP_POST_VARS['id'];
			$sql="select `type` from `" . $this->itemsTable . "` where `node`=$theNode and `id`=$CurrentID";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror['message']);
			};
			if($row=$this->dbc->sql_fetchrow()){
				if(($row["type"]=="file")&&($HTTP_POST_VARS["updatefile"]=="on")){
					if($HTTP_POST_FILES["newfile"]){
						$filename=$HTTP_POST_FILES["newfile"]["name"];
						$dotpos = strrpos($filename,'.');
						$fileExt = substr ($filename,$dotpos);
						$newFileName=edit_find_freefilename($uploadedfilesdir . "article_item_" . $CurrentID . "_", 1, $fileExt);
						if(!copy($HTTP_POST_FILES["newfile"]["tmp_name"], $doc_root . $newFileName)){
							$newFileName="";
						};
					}else{
						$newFileName="";
					};
					if(strlen($newFileName)>0){
						$newFileName=substr($newFileName,strlen($uploadedfilesdir));
						$sql="update `" . $this->itemsTable . "` set `filename`='$newFileName' where `id`=" . $CurrentID . " and `node`=" . $theNode;
						if(!$this->dbc->sql_query($sql)){
							$sqlerror=$this->dbc->sql_error();
							die($sqlerror['message']);
						};
					};
				};
				$pubdate=PostToDate('pubdate');
				$name=(isset($HTTP_POST_VARS['name']))?$HTTP_POST_VARS['name']:'';
				$visible=($HTTP_POST_VARS["visible"]=='on')?1:0;
				$deleted=($HTTP_POST_VARS["delete"]=='on')?1:0;
				$sql="update `" . $this->itemsTable . "` set `pubdate`=$pubdate, `name`='$name', `visible`=$visible, `deleted`=$deleted where `id`=" . $CurrentID . " and `node`=" . $theNode;
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror['message']);
				};
                        };
			$mod_future_action="insert";
		};

		if($mod_future_action=='insert'){
			$sql="select `rtext`,`doctype`, `template` from `" . $this->prefsTable . "` where `node`=" . $theNode;
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror['message']);
			};
			if($row=$this->dbc->sql_fetchrow()){
				$doctype=$row["doctype"];
			}else{
				$doctype="text";
			};
			$selfile=($doctype=="file")?" selected":"";
			$seltext=($doctype=="text")?" selected":"";
			$retVal.="<center>" . drwTableBegin('500','') . "<tr><td class=colheader colspan=2>добавить новую публикацию</td></tr>" . $theFormPrefix . "<input type=hidden name=mod_action value=\"" . $mod_future_action . "\">";
			$retVal.="<tr><td class=data1 align=right>Название:</td><td class=data1 align=left><input type=text class=text size=60 name=name></td></tr>";
			$retVal.="<tr><td class=data2 align=right>Тип:</td><td class=data2 align=left><select name=type><option value=text$seltext>текст<option value=file$selfile>файл</select></td></tr>";
			$retVal.="<tr><td class=data1 align=center colspan=2><input type=submit class=button value=\"добавить\"></td></tr>";
			$retVal.="</form>" . drwTableEnd() . "</center>";
			$retVal.="<br>";
			$retVal.=$this->MakeAdminItemsList($theNode,$theFormPrefix,$PageNum);
		};
		return $retVal;
	}


	function MakeAdminItemsList($theNode,$theFormPrefix,$thePage){
		$retVal='';
		$retVal.=drwTableBegin('100%','') . "<tr><td class=colheader colspan=4>существующие статьи</td></tr>";
		$PageSize=$this->prms['AdminPageSize']->Value;
		$PageNum=$thePage;
		$sql="select `id`, `node`, `name`, `type`, `rtext`, `filename`, `docid`, `pubdate`, `visible`, `deleted` from `" . $this->itemsTable . "` where `deleted`<>1 and `node`=" . $theNode . " order by `pubdate` desc";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		};
		$counter=0;
		$tdclass='data1';
		while($row=$this->dbc->sql_fetchrow()){
			$counter++;
			if(($counter>(($PageNum-1)*$PageSize))&&($counter<=(($PageNum)*$PageSize))){
				$wdate=date("d.m.Y",$row['pubdate']);
				$retVal.="<tr><td class=$tdclass align=center>[$wdate]</td><td class=$tdclass>" . CutQuots($row['name']) . "</td>";
				$retVal.="<td class=$tdclass align=center><input type=button value=\"редакт.\" class=button onclick=\"mod_articles_action('edit'," . $row['id'] . ")\"></td>";
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
					$PagerStr.="<a href=\"javascript:mod_articles_action('',0,$fcounter)\">$fcounter</a> ";
				};
			};
			$PagerStr.="</td></tr>" . drwTableEnd();
		};
		$retVal.=$PagerStr;
		return $retVal;
	}


	function MakeAdminEditorScreen($theNode,$theFormPrefix,$theID, $thePage){
		global $uploadedfilesdir;
		$retVal='';
		$sql="select `id`, `node`, `name`, `type`, `rtext`, `filename`, `docid`, `pubdate`, `visible`, `deleted` from `" . $this->itemsTable . "` where `deleted`<>1 and `node`=$theNode and `id`=$theID";
		if(!$this->dbc->sql_query($sql)){
		    $sqlerror=$this->dbc->sql_error();
		    die($sqlerror['message']);
		};
		if($this->dbc->sql_numrows()==0){
			$retVal='Такой статьи не существует';
			return retVal;
		};
		$row=$this->dbc->sql_fetchrow();
		$wdate=date("d.m.Y",$row['pubdate']);
		$retVal.=drwTableBegin('100%','') . "<tr><td class=colheader colspan=2>редактирование публикации<br><a class=header href=\"javascript:mod_articles_action('',0)\">[вернуться к списку публикаций раздела]</a></td></tr>" . $theFormPrefix;
		$retVal.="<input type=hidden name=mod_action value=update><input type=hidden name=id value=$theID><input type=hidden name=page value=$thePage>";
		$retVal.="<tr><td class=data1 align=right>Название:</td><td class=data1 align=left><input type=text class=text name=name value=\"" . CutQuots($row['name']) . "\" size=60 maxlength=250></td></tr>";
		$retVal.="<tr><td class=data2 align=right>Дата публикации:</td><td class=data2 align=left>" . DatePicker('pubdate',$row['pubdate']) . "</td></tr>";
		$visible=($row['visible']==1)?' checked':'';
		$retVal.="<tr><td class=data1 align=right>Доступность на сайте:</td><td class=data1 align=left><input type=checkbox name=visible$visible></td></tr>";
		$formsuffix="";
		switch($row["type"]){
			case "text":{
				$retVal.="<tr><td class=data2 align=center colspan=2>Текст публикации:</td></tr><tr><td class=data2 colspan=2>";
				$retVal.="<iframe name=\"mod_articles_editor\" border=0 width=100% height=700 marginwidth=0 marginheight=0 scrolling=yes frameborder=0></iframe>";
				$retVal.="</td></tr>";
				$formsuffix.="<form method=post action=post.php enctype=\"multipart/form-data\" name=mod_articles_go_form target=mod_articles_editor><input type=hidden name=textID value=" . $row['rtext'] . "></form>";
				$formsuffix.="<script>document.forms['mod_articles_go_form'].submit();</script>";
				break;
			};
			case "file":{
				$thefilelink=($row["filename"]!="")?"<a target=_blank href=\"$uploadedfilesdir" . $row["filename"] . "\">" . $row["filename"] . "</a>":"отсутствует";
				$retVal.="<tr><td class=data2 align=right>Существующий файл:</td><td class=data2 align=left>$thefilelink</td></tr><input type=hidden name=oldfile value=\"" . CutQuots($row["filename"]) . "\">";
				$retVal.="<tr><td class=data1 align=right>Обновить файл:</td><td class=data1 align=left><input type=checkbox name=updatefile></td></tr>";
				$retVal.="<tr><td class=data2 align=right>Новый файл:</td><td class=data2 align=left><input type=file class=text name=newfile></td></tr>";
				break;
			};
		};
		$retVal.="<tr><td colspan=2 class=data2 align=center><input type=checkbox name=delete> - удалить&nbsp;&nbsp;&nbsp;<input type=submit class=button value=\"обновить\"></td></tr>";
		$retVal.="</form>" . $formsuffix . drwTableEnd();
		return $retVal;
	}

	function MakeUserOuput($theNode, $theFormPrefix){
		global $HTTP_GET_VARS, $uploadedfilesdir;
		$retVal=array();
		$PageNum=$HTTP_GET_VARS['page'];
		$PageNum=($PageNum>0)?$PageNum:1;
		$clientslisting="";
		$needarticle=$HTTP_GET_VARS["a"];
		$itemslists=$this->MakeUserItemsListing($theNode, $theFormPrefix,$PageNum,$needarticle);
		$pagechanger=$this->MakePageChanger($theNode, $theFormPrefix,$PageNum);
		$minislotlisting=str_replace("--itemslist--",$itemslists[1],$this->prms['MinislotTemplate']->Value);
		if($needarticle>0){
			$sql="select `id`, `node`, `name`, `type`, `rtext`, `filename`, `docid`, `pubdate`, `visible`, `deleted` from `" . $this->itemsTable . "` where `deleted`<>1 and `visible`=1 and `node`='$theNode' and `id`='$needarticle'";
			if(!$this->dbc->sql_query($sql)){
			    $sqlerror=$this->dbc->sql_error();
			    die($sqlerror['message']);
			};
			if($row=$this->dbc->sql_fetchrow()){
				switch($row['type']){
					case "text":{
						$rv=$this->prms['ItemFullTemplate']->Value;
						$rv=str_replace("--hreflist--",$theFormPrefix,$rv);
						$rv=str_replace("--text--",html_display($row['rtext']),$rv);
						$rv=str_replace("--date--",date($this->prms['DateFormat']->Value,$row['pubdate']),$rv);
						$rv=str_replace("--name--",CutQuots($row['name']),$rv);
						$retVal[0]=$rv;
						$retVal[1]=$minislotlisting;
						return $retVal;
						break;
					};
					case "file":{
						header("Location: $uploadedfilesdir" . $row["filename"]);
						return;
						break;
					};
				};
			};
		};
		$sql="select `prefs`.`rtext` as `rtext`, `prefs`.`doctype` as `doctype`, `prefs`.`template` as `template`, `texts`.`text` as `text` from `" . $this->prefsTable . "` as `prefs` inner join `texts` on `texts`.`id`=`prefs`.`rtext` where `node`='$theNode'";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		};
		if($this->dbc->sql_numrows()==0)return "";
		$row=$this->dbc->sql_fetchrow();
		$clientslisting=$this->prms['PageTemplate']->Value;
		$clientslisting=str_replace("--preamble--",$row["text"],$clientslisting);
		$clientslisting=str_replace("--itemslist--",$itemslists[0],$clientslisting);
		$clientslisting=str_replace("--pager--",$pagechanger,$clientslisting);
		$retVal[0]=$clientslisting;
		return $retVal;
	}

	function MakeUserItemsListing($theNode, $theFormPrefix, $PageNum,$needarticle=0){
		$Slot1="";
		$Slot2="";
		$PageSize=$this->prms['PageSize']->Value;
		$ItemTemplate1=$this->prms['ItemTemplate']->Value;
		$ItemTemplate2=$this->prms['MinislotItemTemplate']->Value;
		$ItemTemplate3=$this->prms['MinislotSelectedItemTemplate']->Value;
		$Devider1=$this->prms['ItemsDevider']->Value;
		$Devider2=$this->prms['MinislotItemsDevider']->Value;
		$sql="select `id`, `node`, `name`, `type`, `rtext`, `filename`, `docid`, `pubdate`, `visible`, `deleted` from `" . $this->itemsTable . "` where `deleted`<>1 and `visible`=1 and `node`='$theNode' order by `pubdate` desc";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		};
		$counter=0;
		while($row=$this->dbc->sql_fetchrow()){
			$counter++;
			$shifted=$counter;
			$InPage=(($shifted>(($PageNum-1)*$PageSize))&&($shifted<=(($PageNum)*$PageSize)));
			$InPage=$InPage||(($PageNum==1)&&($shifted<=0));
			if($InPage){
				$OneItem=$ItemTemplate1;
				$OneItem=str_replace("--date--",date($this->prms['DateFormat']->Value,$row['pubdate']),$OneItem);
				$OneItem=str_replace("--name--",CutQuots($row['name']),$OneItem);
				$OneItem=str_replace("--href--",$theFormPrefix . "&a=" . $row['id'],$OneItem);
				$OneItem=str_replace("--text--",CutQuots($row['text']),$OneItem);
				$Slot1.=$OneItem . $Devider1;
			};
			$OneItem=($needarticle==$row['id'])?$ItemTemplate3:$ItemTemplate2;
			$OneItem=str_replace("--date--",date($this->prms['DateFormat']->Value,$row['pubdate']),$OneItem);
			$OneItem=str_replace("--name--",CutQuots($row['name']),$OneItem);
			$OneItem=str_replace("--href--",$theFormPrefix . "&a=" . $row['id'],$OneItem);
			$OneItem=str_replace("--text--",CutQuots($row['text']),$OneItem);
			$Slot2.=$OneItem . $Devider2;
		};
		$this->ListingSize=$shifted;
		if(strlen($Slot1)>strlen($Devider1))$Slot1=substr($Slot1,0,(strlen($Slot1)-strlen($Devider1)));
		if(strlen($Slot2)>strlen($Devider2))$Slot2=substr($Slot2,0,(strlen($Slot2)-strlen($Devider2)));
		$retVal=array($Slot1,$Slot2);
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

	function CreateStructures($theNode){
		$rtext=text_create_new();
		$sql="insert into `" . $this->prefsTable . "` (`node`, `rtext` , `doctype`, `template`) values ($theNode,$rtext,'text',0)";
		$this->dbc->sql_query($sql);
	}

	function DeleteStructures($theNode){
		$sql="delete from `" . $this->prefsTable . "` where node=$theNode";
		$this->dbc->sql_query($sql);
	}

	function SearchString($theText){
		$retVal=array();
		$sql="select `at`.`id` as `id`, `at`.`node` as `node`, `at`.`name` as `name`, `at`.`pubdate` as `pubdate`, `texts`.`text` as `text`, `at`.`visible` as `visible`, `at`.`deleted` as `deleted`";
		$sql.=" from `" . $this->itemsTable . "` as `at`, `texts` where `at`.`deleted`<>1 and `at`.`visible`=1 and `texts`.`id` = `at`.`rtext` and (UPPER(`texts`.`text`) like UPPER('%" . str_replace("'","''",$theText) . "%'))";
		$sql.=" order by `at`.`pubdate` desc";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		};
		while($row=$this->dbc->sql_fetchrow()){
		  	$foundText=striphtml($row['text']);
		    	$charpos=strpos(strtoupper($foundText),strtoupper($theText));
		    	$textPreview='';
		    	if($charpos>=0){
		    		$textPreview="<strong><u>" . substr($foundText,$charpos,strlen($theText)) . "</u></strong>";
				$BeginPos=(($charpos-100)>0)?($charpos-100):0;
				$textPreview="... " . substr($foundText,$BeginPos,$charpos-$BeginPos) . $textPreview . substr($foundText,$charpos+strlen($theText),100) . " ...";
			};
			$retVal[$counter]=new cslSearchResult();
			$retVal[$counter]->Node=$row['node'];
			$retVal[$counter]->LinkName=$row['name'];
			$retVal[$counter]->ResultPreview=$textPreview;
			$retVal[$counter]->QSParams='&a=' . $row['id'];
			$counter++;
		};
		return $retVal;
	}

	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->itemsTable`;
			CREATE TABLE `$this->itemsTable` (
			  `id` int(11) NOT NULL auto_increment,
			  `node` int(11) NOT NULL default '0',
			  `name` text NOT NULL,
			  `type` varchar(50) NOT NULL default '',
			  `rtext` int(11) NOT NULL default '0',
			  `filename` varchar(250) NOT NULL default '',
			  `docid` int(11) NOT NULL default '0',
			  `pubdate` int(11) NOT NULL default '0',
			  `visible` int(11) NOT NULL default '0',
			  `deleted` int(11) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			);
			DROP TABLE IF EXISTS `$this->prefsTable`;
			CREATE TABLE `$this->prefsTable` (
			  `node` int(11) NOT NULL default '0',
			  `rtext` int(11) NOT NULL default '0',
			  `doctype` varchar(50) NOT NULL default '',
			  `template` int(11) NOT NULL default '0'
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

$theArticlesModule=new clsArticlesModule('articles','публикации',$db);
$modsArray['articles']=$theArticlesModule;
?>