<?php
class clsFormsModule extends clsModule{


	function clsFormsModule($modName,$modDName,$dbconnector){
	    global $SiteMainURL;
	    parent::clsModule($modName,$modDName,$dbconnector);
	    $this->SearchAble=false;
	    $this->version='1.0.1';
	    $this->helpstring="<p>Модуль реализует организацию форм для ввода данных пользователями сайта и приём заполненных форм (данные отправляются на e-mail и сохраняются в БД сайта).</p>".
		    "<p>После создания раздела данного типа необходимо определить список полей, которые будет заполнять пользователь.</p>";


	    $this->prms["AdminPageSize"]=new ConfigParam("AdminPageSize");
            $this->prms["AdminPageSize"]->Description="Количество отображаемых записей на странице модератора.";
            $this->prms["AdminPageSize"]->DataType='int';
            $this->prms["AdminPageSize"]->Value=10;
            $this->prms["AdminPageSize"]->Protected=true;

	    $this->prms["text"]=new ConfigParam("text");
	    $this->prms["text"]->Description="Текст для замены поля --text-- в шаблоне страницы";
	    $this->prms["text"]->DataType="memo";
	    $this->prms["text"]->Value="Будем признательны, если Вы заполните форму, приведённую ниже.<br>";
	    $this->prms["text"]->Protected=false;

	    $this->prms["template"]=new ConfigParam("template");
	    $this->prms["template"]->Description="Шаблон страницы. Допускаемые для замены значения: text, dataform";
	    $this->prms["template"]->DataType="memo";
	    $this->prms["template"]->Value="<table width=100% border=0 cellpadding=0 cellspacing=0><td width=10 valign=top>&nbsp;</td><td width=* valign=top>--text--<br>--dataform--</td><td width=10 valign=top>&nbsp;</td></tr></table>";
	    $this->prms["template"]->Protected=false;

	    $this->prms["dataformtemplate"]=new ConfigParam("dataformtemplate");
	    $this->prms["dataformtemplate"]->Description="Шаблон отображения формы для ввода. Допускаемые для замены значения: form, items";
	    $this->prms["dataformtemplate"]->DataType="memo";
	    $this->prms["dataformtemplate"]->Value="<table border=0 cellpadding=1 cellspacing=0>--form----items--<tr><td colspan=2 align=center><input type=submit class=button value=\"отправить данные\"></td></form></table>";
	    $this->prms["dataformtemplate"]->Protected=false;
	    
	    $this->prms["itemtemplate"]=new ConfigParam("itemtemplate");
	    $this->prms["itemtemplate"]->Description="Шаблон отображения одного элемента ввода в форме. Допускаемые для замены значения: name, input, suffix, htmlspace";
	    $this->prms["itemtemplate"]->DataType="memo";
	    $this->prms["itemtemplate"]->Value="<tr><td align=right>--name--:</td><td align=left>--input--&nbsp;--suffix--</td></tr>--htmlspace--";
	    $this->prms["itemtemplate"]->Protected=false;

            $this->prms["moderator.mail"]=new ConfigParam("moderator.mail");
            $this->prms["moderator.mail"]->Description="Адрес электронной почты модератора";
            $this->prms["moderator.mail"]->DataType="char";
            $this->prms["moderator.mail"]->Value="webmaster@" . $SiteMainURL;
            $this->prms["moderator.mail"]->Protected=false;

            $this->prms["moderator.notify"]=new ConfigParam("moderator.notify");
            $this->prms["moderator.notify"]->Description="Уведомлять модератора";
            $this->prms["moderator.notify"]->DataType="bool";
            $this->prms["moderator.notify"]->Value=false;
            $this->prms["moderator.notify"]->Protected=false;

            $this->prms["moderator.notify.subject"]=new ConfigParam("moderator.notify.subject");
            $this->prms["moderator.notify.subject"]->Description="Тема письма с уведомлением";
            $this->prms["moderator.notify.subject"]->DataType="char";
            $this->prms["moderator.notify.subject"]->Value="new record";
            $this->prms["moderator.notify.subject"]->Protected=false;

            $this->prms["moderator.notify.body"]=new ConfigParam("moderator.notify.body");
            $this->prms["moderator.notify.body"]->Description="Тело письма с уведомлением о новой записи. Допускаеме для замены значения: text";
            $this->prms["moderator.notify.body"]->DataType="memo";
            $this->prms["moderator.notify.body"]->Value="На сайте $SiteMainURL появилась новая запись.\r\n--text--";
            $this->prms["moderator.notify.body"]->Protected=false;
																																									    
	    $this->modTable="mod_forms";
	    $this->modDataTable="mod_forms_data";

	    $this->datatypes=array('text' => 'простой текст', 'memo' => 'многострочный текст', 'select' => 'список', 'number' => 'число', 'checkbox' => 'крыжик (ДА/НЕТ)');

	}
	function ClientScript($theNode, $theFormPrefix, $thePage=1){
                $LocalthePage=$thePage;
                $retVal='';
                $ModifedFormPrefix=substr($theFormPrefix,0,strlen($theFormPrefix)-1);
                $ModifedFormPrefix.=' name="mod_forms_action_form">';
                $retVal.=$ModifedFormPrefix . "<input type=hidden name=mod_action value=\"\"><input type=hidden name=page value=$thePage><input type=hidden name=param1 value=\"\"></form>";
                $retVal.="<script>";
                $retVal.="function mod_forms_action(theAction,thePage,theParam1){";
                $retVal.="document.forms['mod_forms_action_form'].mod_action.value=theAction;\n";
                $retVal.="if(thePage)document.forms['mod_forms_action_form'].page.value=thePage;";
                $retVal.="if(theParam1)document.forms['mod_forms_action_form'].param1.value=theParam1;";
                $retVal.="document.forms['mod_forms_action_form'].submit();\n";
                $retVal.="};";
                $retVal.="</script>";
                return $retVal;
        }
	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
	    global $HTTP_POST_VARS;
	    $PageSize=$this->prms["AdminPageSize"]->Value;
	    $PageNum=($HTTP_POST_VARS["page"]>0)?$HTTP_POST_VARS["page"]:1;
	    $action=($HTTP_POST_VARS["mod_forms_action"])?$HTTP_POST_VARS["mod_forms_action"]:"";
	    if($action=="insert"){
		$sql="insert into `$this->modTable` (`node`, `name`, `type`, `subdata`, `suffix`, `default`, `testregexp`, `htmlspace`, `sort`) values ($theNode, '" . $HTTP_POST_VARS["name"] . "', '" . $HTTP_POST_VARS["type"] . "', '" . $HTTP_POST_VARS["subdata"] . "', '" . $HTTP_POST_VARS["suffix"] . "', '" . $HTTP_POST_VARS["default"] . "', '" . $HTTP_POST_VARS["testregexp"] . "', '" . $HTTP_POST_VARS["htmlspace"] . "','" . $HTTP_POST_VARS["sort"] . "')";
		if(!$this->dbc->sql_query($sql)){
    		    $sqlerror=$this->dbc->sql_error();
		    die($sqlerror['message']);
		};
	    };
	    if($action=="update"){
		if($HTTP_POST_VARS["delete"]=="on"){
 		    $sql="delete from `$this->modTable` where `id`=" . $HTTP_POST_VARS["id"] . " and `node`=$theNode";
		    if(!$this->dbc->sql_query($sql)){
    			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		    };
		}else{
 		    $sql="update `$this->modTable` set `name`='" . $HTTP_POST_VARS["name"] . "', `subdata`='" . $HTTP_POST_VARS["subdata"] . "', `suffix`='" . $HTTP_POST_VARS["suffix"] . "', `default`='" . $HTTP_POST_VARS["default"] . "', `testregexp`='" . $HTTP_POST_VARS["testregexp"] . "', `htmlspace`='" . $HTTP_POST_VARS["htmlspace"] . "', sort='" . $HTTP_POST_VARS["sort"] . "' where `id`=" . $HTTP_POST_VARS["id"] . " and `node`=$theNode";
		    if(!$this->dbc->sql_query($sql)){
    			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		    };
		};
	    };
	    
	    $retVal=drwTableBegin("100%","");
	    $retVal.=$this->ClientScript($theNode,$theFormPrefix,$PageNum);
	    $retVal.="<tr><td class=header colspan=8 align=center>Редактирование существующих полей формы</td></tr><tr>";
	    $retVal.="<td class=colheader align=center>Название</td>";
	    $retVal.="<td class=colheader align=center>Тип</td>";
	    $retVal.="<td class=colheader align=center>Перечисление для списка (ч-з запятую)</td>";
	    $retVal.="<td class=colheader align=center>Ед. изм.</td>";
	    $retVal.="<td class=colheader align=center>Значение по умолчанию</td>";
	    $retVal.="<td class=colheader align=center>Рег. выражение для проверки</td>";
	    $retVal.="<td class=colheader align=center>html-код после поля</td>";
	    $retVal.="<td class=colheader align=center>порядок</td>";
	    $retVal.="<td class=colheader align=center>&nbsp;</td>";
	    $retVal.="</tr>";
	    $sql="select `id`, `node`, `name`, `type`, `subdata`, `suffix`, `default`, `testregexp`, `htmlspace`, `sort` from `$this->modTable` where node=$theNode order by `sort`, `name`";
	    if(!$this->dbc->sql_query($sql)){
    	        $sqlerror=$this->dbc->sql_error();
		die($sqlerror['message']);
	    };
	    $tdclass="data1";
	    while($row=$this->dbc->sql_fetchrow()){
		$retVal.="$theFormPrefix<input type=hidden name=mod_forms_action value=update><input type=hidden name=id value=" . $row["id"] . ">";
		$retVal.="<tr>";
		$retVal.="<td class=$tdclass align=center><input type=text class=text name=name value=\"" . CutQuots($row["name"]) . "\"></td>";
		$retVal.="<td class=$tdclass align=center nowrap>" . $this->datatypes[$row["type"]] . "</td>";
		$retVal.="<td class=$tdclass align=center><input type=text class=text name=subdata value=\"" . CutQuots($row["subdata"]) . "\"></td>";
		$retVal.="<td class=$tdclass align=center><input type=text class=text name=suffix value=\"" . CutQuots($row["suffix"]) . "\"></td>";
		$retVal.="<td class=$tdclass align=center><input type=text class=text name=default value=\"" . CutQuots($row["default"]) . "\"></td>";
		$retVal.="<td class=$tdclass align=center><input type=text class=text name=testregexp value=\"" . CutQuots($row["testregexp"]) . "\"></td>";
		$retVal.="<td class=$tdclass align=center><input type=text class=text name=htmlspace value=\"" . CutQuots($row["htmlspace"]) . "\"></td>";
		$retVal.="<td class=$tdclass align=center><input type=text class=text name=sort size=4 value=\"" . CutQuots($row["sort"]) . "\"></td>";
		$retVal.="<td class=$tdclass align=center nowrap><input type=checkbox name=delete>-удалить&nbsp;<input type=submit class=button value=\"обновить\"></td>";
		$retVal.="</tr></form>";
		$tdclass=($tdclass=="data1")?"data2":"data1";
	    };
	    $retVal.=drwTableEnd();
	    $retVal.="<br><br>" . drwTableBegin("100%","");
	    $retVal.="<tr><td class=header colspan=8 align=center>Добавить новое поле</td></tr><tr>";
	    $retVal.="<td class=colheader align=center>Название</td>";
	    $retVal.="<td class=colheader align=center>Тип</td>";
	    $retVal.="<td class=colheader align=center>Перечисление для списка (ч-з запятую)</td>";
	    $retVal.="<td class=colheader align=center>Ед. изм.</td>";
	    $retVal.="<td class=colheader align=center>Значение по умолчанию</td>";
	    $retVal.="<td class=colheader align=center>Рег. выражение для проверки</td>";
	    $retVal.="<td class=colheader align=center>html-код после поля</td>";
	    $retVal.="<td class=colheader align=center>порядок</td>";
	    $retVal.="<td class=colheader align=center>&nbsp;</td>";
	    $retVal.="</tr>";
	    $retVal.="$theFormPrefix<input type=hidden name=mod_forms_action value=insert>";
	    $retVal.="<tr>";
	    $retVal.="<td class=$tdclass align=center><input type=text class=text name=name value=\"\"></td>";
	    $retVal.="<td class=$tdclass align=center><select name=type>";
	    foreach($this->datatypes as $aKey=>$aVal)$retVal.="<option value=$aKey>$aVal";
	    $retVal.="</select></td>";
	    $retVal.="<td class=$tdclass align=center><input type=text class=text name=subdata value=\"\"></td>";
	    $retVal.="<td class=$tdclass align=center><input type=text class=text name=suffix value=\"\"></td>";
	    $retVal.="<td class=$tdclass align=center><input type=text class=text name=default value=\"\"></td>";
	    $retVal.="<td class=$tdclass align=center><input type=text class=text name=testregexp value=\"\"></td>";
	    $retVal.="<td class=$tdclass align=center><input type=text class=text name=htmlspace value=\"\"></td>";
	    $retVal.="<td class=$tdclass align=center><input type=text class=text name=sort value=\"10\" size=4></td>";
	    $retVal.="<td class=$tdclass align=center nowrap><input type=submit class=button value=\"добавить\"></td>";
	    $retVal.="</tr></form>";
	    $retVal.=drwTableEnd();
	    $retVal.="<br><br>" . drwTableBegin("100%","");
	    $retVal.="<tr><td class=header colspan=3 align=center>Просмотр оставленных данных</td></tr><tr>";
            $sql="select `id`, `node`, `writerip`, `data`, `date`, `remark` from `$this->modDataTable` where `node`=$theNode order by `date` desc";
            if(!$this->dbc->sql_query($sql)){
                    $sqlerror=$this->dbc->sql_error();
                    die($sqlerror["message"]);
            };
            $tdclass="data1";
            $counter=0;
            while($row=$this->dbc->sql_fetchrow()){
                    $counter++;
                    if(($counter>(($PageNum-1)*$PageSize))&&($counter<=(($PageNum)*$PageSize))){
			$data=CutQuots($row["data"]);
			$data=str_replace("\r\n","<br>",$data);
			$retVal.="$theFormPrefix<input type=hidden name=mod_forms_action value=dropdata><input type=hidden name=id value=" . $row["id"] . ">";
			$retVal.="<tr><td class=$tdclass><strong>" . date("d.m.Y H:i",$row["date"]) . ", " . $row["writerip"] . "</strong>&nbsp;<input type=submit class=button value=\"удалить\"><br>";
			$retVal.=$data . "</td></tr></form>";
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
                                    $PagerStr.="<a href=\"javascript:mod_forms_action('',$fcounter,'')\">$fcounter</a> ";
                            };
                    };
                    $PagerStr.="</td></tr>" . drwTableEnd();
            };
            $retVal.=$PagerStr;
	    return $retVal;
	}

	function MakeUserOuput($theNode, $theFormPrefix){
	    global $HTTP_POST_VARS, $coreParams, $client_ip, $client_referer;
	    $mod_action=$HTTP_POST_VARS["mod_action"];
	    $retVal=array();
	    $sql="select `id`, `node`, `name`, `type`, `subdata`, `suffix`, `default`, `testregexp`, `htmlspace`, `sort` from `$this->modTable` where node=$theNode order by `sort`, `name`";
	    if(!$this->dbc->sql_query($sql)){
    	        $sqlerror=$this->dbc->sql_error();
		die($sqlerror['message']);
	    };
	    $fieldlist=$this->dbc->sql_fetchrowset();
	    if($mod_action=="write"){
	        $getteddata="";
		foreach($fieldlist as $row){
		    if($row["type"]=="checkbox"){
			$fielddata=($HTTP_POST_VARS["input" . $row["id"]]=="on")?"ДА":"НЕТ";
		    }else{
			$fielddata=$HTTP_POST_VARS["input" . $row["id"]];
		    };
		    $getteddata.=$row["name"] . ": " . $fielddata . " " . $row["suffix"] . "\r\n";
		};
		$sql="insert into `$this->modDataTable` (`node`, `writerip`, `data`, `date`, `remark`) values (" . $theNode . ", '$client_ip', '$getteddata', " . time() . ", '')";
		if(strpos($client_referer,"written")==FALSE){
    		    $this->dbc->sql_query($sql);
		    if($this->prms["moderator.notify"]->Value){
        		$fromemail=$coreParams["webmasteremail"]->Value;
	        	$mailheader="MIME-Version: 1.0\nContent-type: text/plain; charset=windows-1251\nDate: " . gmdate('D, d M Y H:i:s', time()) . " UT\nX-Priority: Normal\nX-Mailer: PHP\n";
        		$mailheader.="From: " . $fromemail . "<$fromemail>\n";
            		$MessageTS=$this->prms["moderator.notify.body"]->Value;
            		$MessageTS=str_replace("--text--",$getteddata,$MessageTS);
            		if(mail($this->prms["moderator.mail"]->Value,$this->prms["moderator.notify.subject"]->Value,$MessageTS,$mailheader)){
                	                //;
            		};
		    };
		};
            	header ("Location: $theFormPrefix&written");
	    };
	    $items="";
	    $REarrays="var testRE=new Array();\r\n";
	    foreach($fieldlist as $row){
		$item=$this->prms["itemtemplate"]->Value;
		switch ($row["type"]){
		    case "text":{
			    $input="<input type=text class=text size=40 value=\"" . CutQuots($row["default"]) . "\" name=input" . $row["id"] . ">";
			    break;
			};
		    case "memo":{
			    $input="<textarea name=input" . $row["id"] . " cols=70 rows=6>" . CutQuots($row["default"]) . "</textarea>";
			    break;
			};
		    case "number":{
			    $input="<input type=text class=text size=40 style=\"text-align:right\" value=\"" . CutQuots($row["default"]) . "\" name=input" . $row["id"] . ">";
			    break;
			};
		    case "select":{
			    $input="<select size=1 name=input" . $row["id"] . ">";
			    $datas=split("\,",$row["subdata"]);
			    foreach($datas as $data){
				$selected=($row["default"]==$data)?" selected":"";
				$input.="<option$selected values=\"" . CutQuots($data) . "\">" . CutQuots($data) . "</option>";
			    };
			    break;
			};
		    case "checkbox":{
			    $checked=($row["default"]=="ДА")?" checked":"";
			    $input="<input type=checkbox name=input" . $row["id"] . "$checked>";
			    break;
			};
		    default:
		};
		if($row["testregexp"]!="")$REarrays.="testRE['input" . $row["id"] . "']=new Array(/" . $row["testregexp"] . "/gi,'" . CutQuots($row["name"]) . "');\r\n";
		$item=str_replace("--input--",$input,$item);
		$item=str_replace("--name--",CutQuots($row["name"]),$item);
		$item=str_replace("--suffix--",CutQuots($row["suffix"]),$item);
		$item=str_replace("--htmlspace--",$row["htmlspace"],$item);
		$items.=$item;
	    };
	    $dataform=str_replace("--items--",$items,$this->prms["dataformtemplate"]->Value);
	    $dataform=str_replace("--form--","<form method=post action=\"$theFormPrefix\" onsubmit=\"return checkformsform(this);\"><input type=hidden name=mod_action value=write>",$dataform);
	    $retVal[0]=$this->prms["template"]->Value;
	    $retVal[0]=str_replace("--text--",$this->prms["text"]->Value,$retVal[0]);
	    $retVal[0]=str_replace("--dataform--",$dataform,$retVal[0]);
	    $retVal[0].="<script>\r\n$REarrays\r\n" . 
		    "function checkformsform(frm){".
		    "var thevalue;".
		    "for(aKey in testRE){".
		    "thevalue=String(frm[aKey].value);".
		    "if(!thevalue.match(testRE[aKey][0])){".
		    "alert('Некорректно заполнено поле \"'+testRE[aKey][1]+'\"');".
		    "frm[aKey].focus();".
		    "return false;".
		    "}".
		    "};return true;".
		    "}";
	    $retVal[0].="</script>";
	    return $retVal;
	}


	function CreateStructures($theNode){
	}

	function DeleteStructures($theNode){
	    $sql="delete from `$this->modTable` where `node`=$theNode";
	    $this->dbc->sql_query($sql);
	}

	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->modTable`;
			CREATE TABLE `$this->modTable` (
				`id` int(11) NOT NULL auto_increment,
				`node` int(11) NOT NULL,
			        `name` varchar(250) NOT NULL,
			        `type` varchar(50) NOT NULL,
				`subdata` varchar(250) NOT NULL,
				`suffix` varchar(250) NOT NULL,
				`testregexp` varchar(250) NOT NULL,
				`sort` int(11) NOT NULL,
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

$theFormsModule=new clsFormsModule('forms','формы',$db);
$modsArray['forms']=$theFormsModule;
?>