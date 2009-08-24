<?php
class clsAdeModule extends clsModule{


	function clsAdeModule($modName,$modDName,$dbconnector){
	    global $SiteMainURL;
	    parent::clsModule($modName,$modDName,$dbconnector);
	    $this->SearchAble=false;
	    $this->version='1.0.1';
	    $this->helpstring="<p>Модуль реализует списки организаций - членов или кандидатов в АДЭ.</p>";


	    $this->prms["AdminPageSize"]=new ConfigParam("AdminPageSize");
            $this->prms["AdminPageSize"]->Description="Количество отображаемых записей на странице модератора.";
            $this->prms["AdminPageSize"]->DataType='int';
            $this->prms["AdminPageSize"]->Value=60;
            $this->prms["AdminPageSize"]->Protected=true;

	    $this->prms["show.members"]=new ConfigParam("show.members");
	    $this->prms["show.members"]->Description="Отображать в пользовательском списке членов ассоциации";
	    $this->prms["show.members"]->DataType="bool";
	    $this->prms["show.members"]->Value=false;
	    $this->prms["show.members"]->Protected=false;
	    
	    $this->prms["show.candidats"]=new ConfigParam("show.candidats");
	    $this->prms["show.candidats"]->Description="Отображать в пользовательском списке кандидатов в члены ассоциации";
	    $this->prms["show.candidats"]->DataType="bool";
	    $this->prms["show.candidats"]->Value=false;
	    $this->prms["show.candidats"]->Protected=false;

	    $this->prms["listheader"]=new ConfigParam("listheader");
	    $this->prms["listheader"]->Description="Заголовок таблицы";
	    $this->prms["listheader"]->DataType="char";
	    $this->prms["listheader"]->Value="Список членов АДЭ";
	    $this->prms["listheader"]->Protected=false;

	    $this->modTable="mod_ade_members";
	    $this->modFilesTable="mod_ade_members_files";
	    $this->modCommentsTable="mod_ade_comments";
	    $this->filesplace="/media/ade/";
	}

	function ClientScript($theNode, $theFormPrefix, $thePage=1){
		$LocalthePage=$thePage;
		$retVal='';
		$ModifedFormPrefix=substr($theFormPrefix,0,strlen($theFormPrefix)-1);
		$ModifedFormPrefix.=' name="mod_ade_action_form">';
		$retVal.=$ModifedFormPrefix . "<input type=hidden name=mod_action value=\"\"><input type=hidden name=page value=$thePage><input type=hidden name=param1 value=\"\"></form>";
		$retVal.="<script>";
		$retVal.="function mod_ade_action(theAction,thePage,theParam1){";
		$retVal.="document.forms['mod_ade_action_form'].mod_action.value=theAction;\n";
		$retVal.="if(thePage)document.forms['mod_ade_action_form'].page.value=thePage;";
		$retVal.="if(theParam1)document.forms['mod_ade_action_form'].param1.value=theParam1;";
		$retVal.="document.forms['mod_ade_action_form'].submit();\n";
		$retVal.="};";
		$retVal.="</script>";
		return $retVal;
        }
	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
		return "";
	}

	function MakeUserOuput($theNode, $theFormPrefix){
		global $HTTP_GET_VARS, $HTTP_POST_VARS, $coreParams, $client_ip, $client_referer, $SiteMainURL;
		$retVal=array();
		$retVal[0]="";
		if(is_numeric($HTTP_GET_VARS["mid"])){
			$sql="select `m`.`id` as `mid`, `m`.`serial` as `mserial`, `m`.`name` as `mname`, `m`.`addr_u`, `m`.`addr_f`, `m`.`signaturer`, `m`.`contacts`, `m`.`candidat`, `m`.`notifymail` as `notifymail`,
	    			`f`.`name` as `fname`, `f`.`type`, `f`.`width`, `f`.`height`, `f`.`serial` as `fserial`, `f`.`filename`
	    			from `$this->modTable` as `m` left join `$this->modFilesTable` as `f` on `f`.`member`=`m`.`id` where `m`.`public`=1 and (`f`.`public`=1 or `f`.`public` is null) and `m`.`serial`=" . $HTTP_GET_VARS["mid"];
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror['message']);
			};
			if($row=$this->dbc->sql_fetchrow()){
					if(($HTTP_POST_VARS["mod_action"]=="writecomment")&&(strlen($HTTP_POST_VARS["contact"])>0)){
						$mailtext="Внимание! На сайте $SiteMainURL появился новый отзыв о кандидате или участнике АДЭ.\r\n";
						$mailtext.="Отзыв об организации: " . $row["mname"] . " (" . $row["mserial"] . ")\r\n";
						$mailtext.="Контактная информация отправителя: " . stripslashes($HTTP_POST_VARS["contact"]) . "\r\n\r\n";
						$mailtext.="Текст отзыва: " . stripslashes($HTTP_POST_VARS["text"]) . "\r\n";
						$mailheader="MIME-Version: 1.0\nContent-type: text/plain; charset=windows-1251\nDate: " . gmdate("D, d M Y H:i:s", time()) . " UT\nX-Priority: 3\nX-MSMail-Priority: Normal\nX-Mailer: PHP";
//						ini_set("SMTP","gmail-smtp-in.l.google.com");
//						ini_set("sendmail_from","delfinius@gmail.com");
						$sql="insert into `$this->modCommentsTable` (`member`, `author`, `text`, `public`, `author_ip`, `writedate`) values (" . $row["mid"] . ", '" . $HTTP_POST_VARS["contact"] . "', '" . $HTTP_POST_VARS["text"] . "',0, '" . $client_ip . "', " . time() . ");";
						$this->dbc->sql_query($sql);
						mail($coreParams["webmasteremail"]->Value,"$SiteMainURL :: new reverse",$mailtext,"$mailheader\nfrom:$SiteMainURL <webmaster@$SiteMainURL>");
						if(strlen($row["notifymail"])>0)
							mail($row["notifymail"],"$SiteMainURL :: новый отзыв",$mailtext,"$mailheader\nfrom:$SiteMainURL <webmaster@$SiteMainURL>");
						$retVal[0]="<table border=0 cellpadding=0 cellspacing=0 width=630><tr><td><h3>Отзыв отправлен</h3>";
						$retVal[0].="<p class=main>Ваш отзыв будет размещен в течение 2-х рабочих дней после отправки.</p>";
						$retVal[0].="<p class=main>Одновременно с модератором АДЭ ваше сообщение получит участник, о котором Вы размещаете информацию!</p>";
						$retVal[0].="<p class=main>Прежде чем ваш отзыв будет размещен на сайте, информация будет проверена модератором  для исключения  использования этого ресурса в целях недобросовестной конкуренции,  клеветы и оговора.</p>";
						$retVal[0].="<a href=\"$theFormPrefix\">вернуться к списку членов АДЭ</a></td></tr></table>";
						return $retVal;
					};
					$sql="select `author`, `text` from `$this->modCommentsTable` where `public`=1 and `member`=" . $row["mid"] . " order by id";
					if(!$this->dbc->sql_query($sql)){
						$sqlerror=$this->dbc->sql_error();
						die($sqlerror['message']);
					};
					$alreadytexts="<table border=0 cellpadding=0 cellspacing=0 width=630><tr><td>";
					$alreadytexts.="<p class=main>Внимание!  Одновременно с модератором АДЭ ваше сообщение получит участник, о котором Вы размещаете информацию! Анонимные отзывы удаляются без рассмотрения!</p>";
					$alreadytexts.="<p class=main>Модератор и администрация сайта оставляют за собой право  удалять или оставлять информацию на сайте без объяснения причин.</p>";
					$alreadytexts.="<p class=main>Правила размещения информации:</p><ul>";
					$alreadytexts.="<li>представьтесь (название компании, город, способ связи, контактное лицо).</li>";
					$alreadytexts.="</ul><p class=main>Вы размещаете информацию, имеющую отношение в конкретной фирме, поэтому</p><ul>";
					$alreadytexts.="<li>убедитесь в ее достоверности;</li>";
					$alreadytexts.="<li>опишите ситуацию, ставшую поводом для сообщения;</li>";
					$alreadytexts.="<li>будьте готовы  (по запросу администратора) предоставить документальное подтверждение ваших претензий.</li></ul>";
					$alreadytexts.="</td></tr></table>";
					if($this->dbc->sql_numrows()>0){
						$alreadytexts="<br>" . drwTableBegin("645",0);
						if($row["candidat"]==1){
							$alreadytexts.="<tr><td class=colheader align=center>Отзывы о кандидате в АДЭ</td></tr>";
						}else{
							$alreadytexts.="<tr><td class=colheader align=center>Отзывы о члене АДЭ</td></tr>";
						};
						while($comment=$this->dbc->sql_fetchrow()){
							$alreadytexts.="<tr><td class=data1><strong>" . CutQuots($comment["author"]) . ":</strong><br><br>";
							$alreadytexts.=str_replace("\r\n","<br>",CutQuots($comment["text"])) . "</td>";
						};
						$alreadytexts.=drwTableEnd();
					};
					$resultrows="<br>" . drwTableBegin("645",0);
					$resultrows.="<form method=post><input type=hidden name=mod_action value=writecomment>";
					if($row["candidat"]==1){
						$resultrows.="<tr><td class=colheader colspan=2 align=center>Ваш отзыв о кандидате в АДЭ</td></tr>";
						$name=CutQuots($row ["mname"]) . "<br>(кандидат)";
					}else{
						$resultrows.="<tr><td class=colheader colspan=2 align=center>Ваш отзыв о члене АДЭ</td></tr>";
						$name=CutQuots($row["mname"]) . "<br>(действительный член)";
					};
					$resultrows.="<tr><td class=data1 align=right valign=top>Наименование организации:</td><td class=data1 align=left>$name</td></tr>";
					$resultrows.="<tr><td class=data2 align=right valign=top>Представьтесь:</td><td class=data2 align=left><textarea name=contact cols=50 rows=3></textarea></td></tr>";
					$resultrows.="<tr><td class=data1 align=right valign=top>Ваш отзыв:</td><td class=data1 align=left><textarea name=text cols=70 rows=10></textarea></td></tr>";
					$resultrows.="<tr><td class=data2 align=center colspan=2><input type=submit class=button value=\"отправить отзыв\"></td></tr>";
					$resultrows.="</form>" . drwTableEnd();
					$retVal[0]=$alreadytexts . $resultrows;
				return $retVal;
			};
		};
		$sql="select `m`.`id` as `mid`, `m`.`serial` as `mserial`, `m`.`name` as `mname`, `m`.`addr_u`, `m`.`addr_f`, `m`.`signaturer`, `m`.`contacts`, `m`.`candidat`,
	    		`f`.`name` as `fname`, `f`.`type`, `f`.`width`, `f`.`height`, `f`.`serial` as `fserial`, `f`.`filename`
	    		from `$this->modTable` as `m` left join `$this->modFilesTable` as `f` on `f`.`member`=`m`.`id` where `m`.`public`=1 and (`f`.`public`=1 or `f`.`public` is null) order by `m`.`candidat` asc,`m`.`serial`, `m`.`name`, `f`.`serial`, `f`.`name`";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		};
		$resultrows="";
		$tdclass="data2";
		$oldid=0;
		while($row=$this->dbc->sql_fetchrow()){
//			if(($this->prms["show.members"]->Value&&($row["candidat"]==0))||($this->prms["show.candidats"]->Value&&($row["candidat"]==1))){
				if($oldid!=$row["mid"]){
					$oldid=$row["mid"];
					$tdclass=($tdclass=="data1")?"data2":"data1";
					if($row["candidat"]==0){
						$tdstyle="style=\"background-color:lightgreen;\"";
						$name=CutQuots($row["mname"]) . "<br>(участник)";
					}else{
						$tdstyle="style=\"background-color:pink;\"";
						$name=CutQuots($row["mname"]) . "<br>(кандидат)";
					};
					$resultrows.="</td></tr><tr>";
					$resultrows.="<td class=$tdclass $tdstyle align=center>" . $row["mserial"] . "</td>";
					$resultrows.="<td class=$tdclass $tdstyle align=left><a href=\"$theFormPrefix&mid=" . $row["mserial"] . "\" title=\"отправить отзыв\">$name</a></td>";
					$resultrows.="<td class=$tdclass align=left>" . CutQuots($row["signaturer"]) . "</td>";
					$resultrows.="<td class=$tdclass align=left><strong>юридический:</strong> " . CutQuots($row["addr_u"]) . "<br><br>";
					$resultrows.="<strong>фактический:</strong> " . CutQuots($row["addr_f"]) . "</td>";
					$resultrows.="<td class=$tdclass align=left nowrap>" . str_replace("\r\n","<br>",CutQuots($row["contacts"])) . "</td>";
					$resultrows.="<td class=$tdclass align=left nowrap>";
				};
				if($row["type"]=="text"){
					$resultrows.="<a href=\"$this->filesplace" . $row["filename"] . "\" target=_blank><img border=0 src=/media/ade/text.gif width=14 height=19 align=absmiddle>" . CutQuots($row["fname"]) . "</a><br>";
				}else if($row["type"]=="picture"){
					$resultrows.="<a href=\"$this->filesplace" . $row["filename"] . "\" target=imageswindow onclick=\"self.open('imageswindow','imageswindow','width=" . $row["width"] . ",height=" . $row["height"] . "')\"><img  border=0 src=/media/ade/picture.gif width=14  align=absmiddle height=19>&nbsp;" . CutQuots($row["fname"]) . "</a><br>";
				}
//			};
		};
		if($resultrows!=""){
			$resultrows="<br>" . drwTableBegin("645",0/*,$this->prms["listheader"]->Value*/) .
				"<tr><td class=colheader align=center>№ пп.</td>".
				"<td class=colheader align=center>название</td>".
				"<td class=colheader align=center>право подписи</td>".
				"<td class=colheader align=center>адрес</td>".
				"<td class=colheader align=center>контакты</td>".
				"<td class=colheader align=center>файлы".
				$resultrows . drwTableEnd() . "<br>".
				"* - карта предприятия<br>".
				"** - свидетельство ИНН<br>".
				"*** - свидетельство ОГРН<br>".
				"**** - страховой полис<br>";
			$retVal[0]=$resultrows;
		};
		return $retVal;
	}

	function MakeParamsOuput($theFormPrefix){
		global $HTTP_POST_VARS, $HTTP_POST_FILES, $uploadedfilesdir, $doc_root;
		$PageSize=$this->prms["AdminPageSize"]->Value;
		$PageNum=($HTTP_POST_VARS["page"]>0)?$HTTP_POST_VARS["page"]:1;
		$retVal=$this->ClientScript(0, $theFormPrefix, $PageNum);
		$action=($HTTP_POST_VARS["mod_ade_action"])?$HTTP_POST_VARS["mod_ade_action"]:"";
		$memberid=($HTTP_POST_VARS["member"])?$HTTP_POST_VARS["member"]:0;
		if($action=="insert"){
			$sql="insert into `$this->modTable` (`serial`, `name`) values ('" . $HTTP_POST_VARS["serial"] . "', '" . $HTTP_POST_VARS["name"] . "')";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror['message']);
			};
			$memberid=$this->dbc->sql_nextid();
		};
		if($action=="update"){
			if($HTTP_POST_VARS["delete"]=="on"){
				$sql="delete from `$this->modTable` where `id`=" . $memberid;
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror['message']);
				};
				$memberid=0;
			}else{
				$candidat=($HTTP_POST_VARS["candidat"]=="on")?1:0;
				$public=($HTTP_POST_VARS["public"]=="on")?1:0;
				$sql="update `$this->modTable` set `serial`='" . $HTTP_POST_VARS["serial"] . "', `name`='" . $HTTP_POST_VARS["name"] . "', `addr_u`='" . $HTTP_POST_VARS["addr_u"] . "', `addr_f`='" . $HTTP_POST_VARS["addr_f"] . "', `signaturer`='" . $HTTP_POST_VARS["signaturer"] . "', `contacts`='" . $HTTP_POST_VARS["contacts"] . "', `notifymail`='" . $HTTP_POST_VARS["notifymail"] . "', `candidat`=$candidat, `public`=$public where `id`=$memberid";
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror['message']);
				};
			};
		};
		if($action=="appendfile"){
			if($HTTP_POST_FILES["file"]){
				$sql="select max(`id`) as `mm` from `$this->modFilesTable`";
				$this->dbc->sql_query($sql);
				$row=$this->dbc->sql_fetchrow();
				$newid=$row["mm"]+1;
				$filename=$HTTP_POST_FILES["file"]["name"];
				$dotpos = strrpos($filename,'.');
				$fileExt = strtolower( substr ($filename,$dotpos) );
				$newFileName="ade_item_$newid$fileExt";
				if(!copy($HTTP_POST_FILES["file"]["tmp_name"], $doc_root . $this->filesplace . $newFileName)){
					$newFileName="";
				};
			}else{
				$newFileName="";
			};
			if($newFileName!=""){
				$isimage=false;
				$image_width=0;
				$image_height=0;
				if(($fileExt==".jpg")||($fileExt==".jpeg")){
					$loadedimage=imagecreatefromjpeg($doc_root . $this->filesplace . $newFileName);
					$isimage=true;
				}else if($fileExt==".png"){
					$loadedimage=imagecreatefrompng($doc_root . $this->filesplace . $newFileName);
					$isimage=true;
				}else if($fileExt==".gif"){
					$loadedimage=imagecreatefromgif($doc_root . $this->filesplace . $newFileName);
					$isimage=true;
				}else{
				};
				if($isimage){
					$image_width = imagesx($loadedimage);
					$image_height = imagesy($loadedimage);
				};
				$filetype=($isimage)?"picture":"text";
				$sql="insert into `$this->modFilesTable` (`member`, `name`, `type`, `width`, `height`, `public`, `serial`, `filename`) values ($memberid, '" . $HTTP_POST_VARS["name"] . "', '$filetype', $image_width, $image_height, 0, '" . $HTTP_POST_VARS["serial"] . "', '$newFileName')";
				$this->dbc->sql_query($sql);
			};
		};
		
		if($action=="updatefile"){
			$public=($HTTP_POST_VARS["public"]=="on")?1:0;
			if($HTTP_POST_VARS["delete"]=="on"){
				$sql="delete from `$this->modFilesTable` where `member`=$memberid and `id`=" . $HTTP_POST_VARS["id"];
				$this->dbc->sql_query($sql);
			}else{
				$sql="update `$this->modFilesTable` set `name`='" . $HTTP_POST_VARS["name"] . "', `serial`='" . $HTTP_POST_VARS["serial"] . "', `public`=$public where `member`=$memberid and `id`=" . $HTTP_POST_VARS["id"];
				$this->dbc->sql_query($sql);
			};
		};
		
		if($action=="updatecomment"){
			$public=($HTTP_POST_VARS["public"]=="on")?1:0;
			if($HTTP_POST_VARS["delete"]=="on"){
				$sql="delete from `$this->modCommentsTable` where `member`=$memberid and `id`=" . $HTTP_POST_VARS["id"];
				$this->dbc->sql_query($sql);
			}else{
				$sql="update `$this->modCommentsTable` set `author`='" . $HTTP_POST_VARS["author"] . "', `text`='" . $HTTP_POST_VARS["text"] . "', `public`=$public where `member`=$memberid and `id`=" . $HTTP_POST_VARS["id"];
				$this->dbc->sql_query($sql);
			};
		};
		
		if($memberid>0){
	    		$sql="select `id`,`serial`, `name`, `addr_u`, `addr_f`, `signaturer`, `contacts`, `candidat`, `public`, `notifymail` from `$this->modTable` where `id`=$memberid";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror['message']);
			};
			if($row=$this->dbc->sql_fetchrow()){
	    			$retVal.=drwTableBegin("100%","");
		    		$retVal.="<tr><td class=colheader colspan=2>Редактирование информации об организации</td></tr>";
		    		$retVal.="$theFormPrefix<input type=hidden name=mod_ade_action value=update><input type=hidden name=member value=$memberid>";
		    		$retVal.="<tr><td class=frm_data1 align=right>Порядковый номер:</td><td class=frm_data1><input type=text class=text name=serial value=\"" . CutQuots($row["serial"]) . "\" size=40></td></tr>";
		    		$retVal.="<tr><td class=frm_data2 align=right>Название организации:</td><td class=frm_data2><input type=text class=text name=name value=\"" . CutQuots($row["name"]) . "\" size=40></td></tr>";
		    		$retVal.="<tr><td class=frm_data1 align=right valign=top>Юридический адрес:</td><td class=frm_data1><textarea name=addr_u cols=50 rows=4>" . CutQuots($row["addr_u"]) . "</textarea></td></tr>";
		    		$retVal.="<tr><td class=frm_data2 align=right valign=top>Фактический адрес:</td><td class=frm_data2><textarea name=addr_f cols=50 rows=4>" . CutQuots($row["addr_f"]) . "</textarea></td></tr>";
		    		$retVal.="<tr><td class=frm_data1 align=right>Право подписи:</td><td class=frm_data1><input type=text class=text name=signaturer value=\"" . CutQuots($row["signaturer"]) . "\" size=40></td></tr>";
		    		$retVal.="<tr><td class=frm_data2 align=right valign=top>Контактная информация:</td><td class=frm_data2><textarea name=contacts cols=50 rows=6>" . CutQuots($row["contacts"]) . "</textarea></td></tr>";
		    		$checked=($row["candidat"]==1)?" checked":"";
		    		$retVal.="<tr><td class=frm_data1 align=right>Кандидат:</td><td class=frm_data1><input type=checkbox name=candidat$checked></td></tr>";
		    		$checked=($row["public"]==1)?" checked":"";
		    		$retVal.="<tr><td class=frm_data2 align=right>Опубликован:</td><td class=frm_data2><input type=checkbox name=public$checked></td></tr>";
		    		$retVal.="<tr><td class=frm_data1 align=right>E-mail для уведомлений:</td><td class=frm_data1><input type=text class=text name=notifymail value=\"" . CutQuots($row["notifymail"]) . "\" size=40></td></tr>";
		    		$retVal.="<tr><td colspan=2 class=frm_data1 align=center><input type=checkbox name=delete>&nbsp;-&nbsp;Удалить&nbsp;&nbsp;<input type=submit class=button value=\"обновить\"></td></tr>";
		    		$retVal.="</form>";
	    			$retVal.=drwTableEnd();
	    			$retVal.=drwTableBegin("100%","");
	    			$retVal.="<tr><td class=colheader colspan=5>файлы</td></tr><tr>";
	    			$retVal.="<td class=data2 align=center>сортировка</td>";
	    			$retVal.="<td class=data2 align=center>название</td>";
				$retVal.="<td class=data2 align=center>&nbsp;</td>";
				$retVal.="<td class=data2 align=center>публикация</td>";
				$retVal.="<td class=data2 align=center>&nbsp;</td></tr>";
	    			$sql="select `id`, `serial`, `member`, `name`, `type`, `width`, `height`, `public`, `filename` FROM `$this->modFilesTable` where `member`=$memberid order by `serial`";
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror['message']);
				};
				$tdclass="data1";
				while($row=$this->dbc->sql_fetchrow()){
					$chartdstyle=($row["public"]==1)?"background-color:lightgreen;":"background-color:pink;";
					$retVal.="$theFormPrefix<input type=hidden name=mod_ade_action value=updatefile><input type=hidden name=id value=" . $row["id"] . "><input type=hidden name=member value=$memberid>";
					$retVal.="<tr><td class=$tdclass style=\"$chartdstyle\" align=center><input type=text class=text name=serial value=" . $row["serial"] . "></td>";
					$retVal.="<td class=$tdclass><input type=text class=text name=name value=\"" . CutQuots($row["name"]) . "\" size=50></td>";
					$retVal.="<td class=$tdclass><a href=\"$this->filesplace" . $row["filename"] . "\" target=_blank>просмотреть</a></td>";
					$checked=($row["public"]==1)?" checked":"";
					$retVal.="<td class=$tdclass><input type=checkbox name=public$checked> - опубликован</td>";
					$retVal.="<td class=$tdclass align=center><input type=checkbox name=delete>&nbsp;-&nbsp;удалить&nbsp;<input type=submit class=button value=\"обновить\"></td></tr>";
					$retVal.="</form>";
					$tdclass=($tdclass=="data1")?"data2":"data1";
				};
	    			$retVal.=drwTableEnd();
	    			$retVal.=drwTableBegin("100%","");
	    			$retVal.="<tr><td class=colheader colspan=4>добавить файл</td></tr>";
				$retVal.="$theFormPrefix<input type=hidden name=mod_ade_action value=appendfile><input type=hidden name=member value=$memberid>";
				$retVal.="<tr><td class=data1 align=center><input type=text class=text name=serial value=10></td>";
				$retVal.="<td class=data1><input type=text class=text name=name size=50 value=\"unnamed\"></td>";
				$retVal.="<td class=data1><input type=file class=text name=file size=50></td>";
				$retVal.="<td class=data1><input type=submit class=button value=\"добавить\"></td>";
				$retVal.="</tr></form>";
	    			$retVal.=drwTableEnd() . "<br>";
	    			$retVal.=drwTableBegin("100%","");
	    			$retVal.="<tr><td class=colheader colspan=5>отзывы посетителей</td></tr><tr>";
	    			$sql="select `id`, `author`, `text`, `author_ip`, `public` from `$this->modCommentsTable` where `member`=$memberid order by `id`";
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror['message']);
				};
				$tdclass="data1";
				while($row=$this->dbc->sql_fetchrow()){
					$chartdstyle=($row["public"]==1)?"background-color:lightgreen;":"background-color:pink;";
					$retVal.="$theFormPrefix<input type=hidden name=mod_ade_action value=updatecomment><input type=hidden name=member value=$memberid><input type=hidden name=id value=" . $row["id"] . ">";
					$retVal.="<tr>";
					$retVal.="<td class=$tdclass style=\"$chartdstyle\"><textarea name=author cols=20 row=4>" . CutQuots($row["author"]) . "</textarea></td>";
					$retVal.="<td class=$tdclass style=\"$chartdstyle\"><textarea name=text cols=40 row=6>" . CutQuots($row["text"]) . "</textarea></td>";
					$checked=($row["public"]==1)?" checked":"";
					$retVal.="<td class=$tdclass style=\"$chartdstyle\"><input type=checkbox name=public$checked> - публиковать<br><input type=checkbox name=delete> - удалить</td>";
					$retVal.="<td class=$tdclass style=\"$chartdstyle\"><input type=submit class=button value=\"обновить\"></td>";
					$retVal.="</tr></form>";
					$tdclass=($tdclass=="data1")?"data2":"data1";
	    			};
	    			$retVal.=drwTableEnd() . "<br>";
			}
		};


		$retVal.=drwTableBegin("100%","");
		$retVal.="<tr><td class=colheader colspan=3>Список членов и кандидатов (маркеры: чёрный - не опубликован, розовый - кандидат, зелёный - член)</td></tr>";
		$sql="select `id`,`serial`, `name`, `addr_u`, `addr_f`, `signaturer`, `contacts`, `candidat`, `public` from `$this->modTable` order by `serial`";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		};
		$tdclass="data1";
		$counter=0;
		while($row=$this->dbc->sql_fetchrow()){
			$counter++;
			if(($counter>(($PageNum-1)*$PageSize))&&($counter<=(($PageNum)*$PageSize))){
				$chartdstyle=($row["candidat"]==1)?"background-color:pink;":"background-color:lightgreen;";
				$chartdstyle=($row["public"]==1)?$chartdstyle:"background-color:black;";
				$retVal.="$theFormPrefix<input type=hidden name=member value=" . $row["id"] . "><tr><td width=2 class=$tdclass style=\"$chartdstyle\">&nbsp;" . CutQuots($row["serial"]) . "&nbsp;</td><td width=100% class=$tdclass>" . CutQuots($row["name"]) . "</td><td class=$tdclass align=center><input type=submit class=button value=\"редактировать\"></td></tr></form>";
				$tdclass=($tdclass=="data1")?"data2":"data1";
			};
		};
		$retVal.=drwTableEnd() . "<br>";

		$PagerStr='';
		$lastPage=($counter%$PageSize==0)?0:1;
		$lastPage+=($counter/$PageSize);
		if($lastPage>=2){
			$PagerStr.=drwTableBegin('100%','') . "<tr><td width=10% nowrap align=right class=colheader>страница:</td><td class=data1>";
			for($fcounter=1;$fcounter<=$lastPage;$fcounter++){
				if($fcounter==$PageNum){
					$PagerStr.="<a style=\"color:gray;text-decoration:none;\">$fcounter</a> ";
				}else{
					$PagerStr.="<a href=\"javascript:mod_ade_action('',$fcounter,'')\">$fcounter</a> ";
				};
			};
			$PagerStr.="</td></tr>" . drwTableEnd() . "<br>";
		};
		$retVal.=$PagerStr;
		
		$sql="select max(`serial`) as `maxserial` from `$this->modTable`";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		};
		$row=$this->dbc->sql_fetchrow();
		$newserial=($row["maxserial"]>0)?$row["maxserial"]+1:0;
		$retVal.=drwTableBegin("100%","") . "$theFormPrefix<input type=hidden name=mod_ade_action value=insert>";
		$retVal.="<tr><td class=colheader colspan=3>Добавить новую организацию</td></tr>";
		$retVal.="<tr><td class=frm_data1>Номер:&nbsp;<input type=text class=text name=serial size=25 value=$newserial></td><td class=frm_data1>Название:&nbsp;<input type=text class=text name=name size=60></td><td class=frm_data1 align=center><input type=submit class=button value=\"добавить\"></td></tr>";
		$retVal.="</form>" . drwTableEnd() . "<br>";
		return $retVal;
	}

	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->modTable`;
			CREATE TABLE `$this->modTable` (
				`id` int(11) NOT NULL auto_increment,
				`serial` int(11) NOT NULL default '0',
				`name` varchar(250) NOT NULL default '',
				`addr_u` varchar(2000) NOT NULL default '',
				`addr_f` varchar(2000) NOT NULL default '',
				`signaturer` varchar(2000) NOT NULL default '',
				`contacts` varchar(4000) NOT NULL default '',
				`candidat` INT(11) NOT NULL default '1',
				`public` INT(11) NOT NULL default '0',
				`notifymail` VARCHAR( 255 ) NOT NULL default '',
				PRIMARY KEY  (`id`)
			);
			DROP TABLE IF EXISTS `$this->modFilesTable`;
			CREATE TABLE `$this->modFilesTable` (
				`id` int(11) NOT NULL auto_increment,
				`member` int(11) NOT NULL default '0',
				`name` varchar(250) NOT NULL default '',
				`type` varchar(250) NOT NULL default '',
				`width` int(11) NOT NULL default '0',
				`height` int(11) NOT NULL default '0',
				`public` INT(11) NOT NULL default '0',
				PRIMARY KEY  (`id`)
			);
			DROP TABLE IF EXISTS `$this->modCommentsTable`;
			CREATE TABLE IF NOT EXISTS `$this->modCommentsTable` (
			    `id` int(11) NOT NULL AUTO_INCREMENT,
			  `member` int(11) NOT NULL,
			  `author` varchar(1000) NOT NULL,
			  `text` longtext NOT NULL,
			  `public` int(11) NOT NULL,
			  `author_ip` varchar(20) NOT NULL,
			  `writedate` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
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

$theAdeModule=new clsAdeModule('ade','АДЭ',$db);
$modsArray['ade']=$theAdeModule;
?>