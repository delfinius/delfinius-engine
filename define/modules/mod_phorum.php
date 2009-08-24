<?php

class clsPhorumModule extends clsModule{


	function clsPhorumModule($modName,$modDName,$dbconnector){
		parent::clsModule($modName,$modDName,$dbconnector);
		$this->SearchAble=true;
		$this->version='1.0.0';
		$this->helpstring='<p>При помощи этого модуля можно создавать на сайте "лёгкие" форумы. Список пользователей один для всего сайта. Для создания раздела форуа создайте раздел в дереве сайта.</p>';

		$this->prms["template"]=new ConfigParam("template");
		$this->prms["template"]->Description="Шаблон вывода страницы с перечнем тем. Допускаемые для замены значения: text, topics";
		$this->prms["template"]->DataType="memo";
		$this->prms["template"]->Value="--text-- --topics--";
		$this->prms["template"]->Protected=false;
		
		$this->prms["AdminPageSize"]=new ConfigParam("AdminPageSize");
		$this->prms["AdminPageSize"]->Description="Количество отображаемых записей о пользователях на странице настройки модуля.";
		$this->prms["AdminPageSize"]->DataType='int';
		$this->prms["AdminPageSize"]->Value=60;
		$this->prms["AdminPageSize"]->Protected=true;
		
		$this->modUsersTable='mod_' . $this->name . '_users';
		$this->modParamsTable='mod_' . $this->name . '_params';
		$this->modAccessTable='mod_' . $this->name . '_access';
		$this->modTopicsTable='mod_' . $this->name . '_topics';
		$this->modPostsTable='mod_' . $this->name . '_posts';

	}
	
	function ClientScript($theNode, $theFormPrefix, $thePage=1){
		$LocalthePage=$thePage;
		$retVal='';
		$ModifedFormPrefix=substr($theFormPrefix,0,strlen($theFormPrefix)-1);
		$ModifedFormPrefix.=' name="mod_' . $this->name . '_action_form">';
		$retVal.=$ModifedFormPrefix . "<input type=hidden name=mod_action value=\"\"><input type=hidden name=page value=$thePage><input type=hidden name=param1 value=\"\"></form>";
		$retVal.="<script>";
		$retVal.='function mod_' . $this->name . '_action(theAction,thePage,theParam1){';
		$retVal.='document.forms[\'mod_' . $this->name . '_action_form\'].mod_action.value=theAction;\n';
		$retVal.='if(thePage)document.forms[\'mod_' . $this->name . '_action_form\'].page.value=thePage;';
		$retVal.='if(theParam1)document.forms[\'mod_' . $this->name . '_action_form\'].param1.value=theParam1;';
		$retVal.='document.forms[\'mod_' . $this->name . '_action_form\'].submit();\n';
		$retVal.='};';
		$retVal.='</script>';
		return $retVal;
	}

	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
		global $HTTP_POST_VARS;
		$retVal='';
		if($HTTP_POST_VARS['mod_' . $this->name . '_action']=='updateparams'){
			$guest_read=($HTTP_POST_VARS['guest_read']=='on')?1:0;
			$guest_write=($HTTP_POST_VARS['guest_write']=='on')?1:0;
			$sql='update `' . $this->modParamsTable . '` set `guest_read`=' . $guest_read . ', `guest_write`=' . $guest_write . ' where node=' . $theNode;
			$this->dbc->sql_query($sql);
		};
		if($HTTP_POST_VARS['mod_' . $this->name . '_action']=='deleteaccess'){
			$sql='delete from `' . $this->modAccessTable . '` where node=' . $theNode . ' and `user`=' . $HTTP_POST_VARS['member'];
			$this->dbc->sql_query($sql);
		};
		if($HTTP_POST_VARS['mod_' . $this->name . '_action']=='insertaccess'){
			$notify=($HTTP_POST_VARS['notify']=='on')?1:0;
			$moderator=($HTTP_POST_VARS['moderator']=='on')?1:0;
			$sql='insert into `' . $this->modAccessTable . '` (`node`,`user`,`notify`,`moderator`) select ' . $theNode . ', `u`.`id`, ' . $notify . ', ' . $moderator . ' from `' . $this->modUsersTable . '` as `u` where `u`.`email`=\'' . $HTTP_POST_VARS['email'] . '\' and `u`.`id` not in (select `user` from `' . $this->modAccessTable . '` where `node`=' . $theNode . ')';
			$this->dbc->sql_query($sql);
		};
		
		$sql='select `rtext`, `guest_read`, `guest_write` from `' . $this->modParamsTable . '` where node=' . $theNode;
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		};
		if($this->dbc->sql_numrows()>0){
			$row=$this->dbc->sql_fetchrow();
			$textID=$row['rtext'];
			$retVal.=drwTableBegin('100%','');
			$checked=($row['guest_read'])?' checked':'';
			$retVal.='<form method=post style="margin:0;"><input type=hidden name="mod_' . $this->name . '_action" value="updateparams">';
			$retVal.='<tr><td class=colheader colspan=2>Параметры анонимного доступа</td></tr>';
			$retVal.='<tr><td class=data1 width=50% align=right>Анонимное чтение раздела</td><td class=data1><input type=checkbox name=guest_read' . $checked . '></td></tr>';
			$checked=($row['guest_write'])?' checked':'';
			$retVal.='<tr><td class=data1 align=right>Анонимная запись</td><td class=data1><input type=checkbox name=guest_write' . $checked . '></td></tr>';
			$retVal.='<tr><td class=data2 colspan=2 align=center><input type=submit class=button value="обновить параметры (вступительный текст обновляется отдельно)"></td></tr>';
			$retVal.='</form>';
			$retVal.='<tr><td class=colheader colspan=2>Вступительный текст раздела</td></tr><tr><td colspan=2><iframe name="mod_' . $this->name . '_text_editor" border=0 width=100% height=400 marginwidth=0 marginheight=0 scrolling=yes frameborder=0></iframe>';
			$retVal.='<form style="margin:0;" method=post action=post.php enctype="multipart/form-data" name="mod_' . $this->name . '_text_go_form" target="mod_' . $this->name . '_text_editor"><input type=hidden name=textID value=' . $textID . '></form></td></tr>';
			$retVal.='<script>document.forms[\'mod_' . $this->name . '_text_go_form\'].submit();</script>';
			$retVal.='<tr><td class=colheader colspan=2>Параметры авторизованного доступа</td></tr>';
			$retVal.='<tr><td class=frm_data1 colspan=2 style="padding:6px;" align=center>' . drwTableBegin('100%','');
			$retVal.='<tr><td class=colheader width=30%>имя</td><td class=colheader width=30% nowrap>эл. почта</td><td class=colheader nowrap>слежение за обновлениями</td><td class=colheader>модератор</td><td class=colheader>&nbsp;</td></tr>';
			$sql='select `u`.`id`, `u`.`email`, `u`.`name`, `a`.`notify`, `a`.`moderator` from `' . $this->modAccessTable . '` as `a` inner join `' . $this->modUsersTable . '` as `u` on `a`.`user`=`u`.`id` where `a`.`node`=' . $theNode;
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror['message']);
			};
			while($row=$this->dbc->sql_fetchrow()){
				$retVal.='<form method=post style="margin:0;"><input type=hidden name="mod_' . $this->name . '_action" value="deleteaccess"><input type=hidden name=member value=' . $row['id'] . '><tr>';
				$retVal.='<td class=data1>' . $row['name'] . '</td>';
				$retVal.='<td class=data1>' . $row['email'] . '</td>';
				$retVal.='<td class=data1>' . (($row['notify']==1)?'ДА':'НЕТ') . '</td>';
				$retVal.='<td class=data1>' . (($row['moderator']==1)?'ДА':'НЕТ') . '</td>';
				$retVal.='<td class=data1><input type=submit class=button value="удалить"></td>';
				$retVal.='<tr></form>';
			};
			$retVal.=drwTableEnd() . '<br>';
			$retVal.=drwTableBegin('100%','');
			$retVal.='<tr><td class=colheader colspan=4>добавить пользователя</td></tr>';
			$retVal.='<form method=post style="margin:0;"><input type=hidden name="mod_' . $this->name . '_action" value="insertaccess">';
			$retVal.='<tr><td class=data1>Эл. почта: <input type=text class=text name=email size=30></td><td class=data1>слежение за обновлениями: <input type=checkbox name=notify></td><td class=data1>модератор: <input type=checkbox name=moderator></td><td class=data1 align=center><input type=submit class=button value="добавить"></td></tr>';
			$retVal.='</form>' . drwTableEnd();
			$retVal.='</td></tr>';
			$retVal.=drwTableEnd();
		};
		return $retVal;
	}

	function MakeUserOuput($theNode, $theFormPrefix){
		$retVal=array();
		$sql="select `texts`.`text` as `text` from `texts` inner join `$this->modParamsTable` on `$this->modParamsTable`.`rtext`=`texts`.`id` where `$this->modParamsTable`.`node`=" . $theNode;
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		};
		if($this->dbc->sql_numrows()>0){
		$row=$this->dbc->sql_fetchrow();
		$retVal[0]=str_replace("--text--",$row['text'],$this->prms["template"]->Value);
		};
		return $retVal;
	}

	function CreateStructures($theNode){
		$textid=text_create_new();
		$sql='insert into `' . $this->modParamsTable . '` (`node`, `rText`) values ($theNode,$textid)';
		$this->dbc->sql_query($sql);
	}

	function DeleteStructures($theNode){
		$sql="delete from `texts` where `id` in (select `rText` from $this->modParamsTable` where `node`=$theNode)";
		$this->dbc->sql_query($sql);
		$sql="delete from `$this->modParamsTable` where `node`=$theNode";
		$this->dbc->sql_query($sql);
	}

	function SearchString($theText){
		global $SiteTree;
		$retVal=array();
		$sql="select `" . $SiteTree->cTreeTable . "`.`" . $SiteTree->cKeyField . "` as `" . $SiteTree->cKeyField . "`, `texts`.`text` as `text` from `" . $SiteTree->cTreeTable . "` inner join `$this->modTable` on `$this->modTable`.`node`=`" . $SiteTree->cTreeTable . "`.`" . $SiteTree->cKeyField . "` inner join `texts` on `texts`.`id`=`$this->modTable`.`rtext` where UPPER(`texts`.`text`) like UPPER('%" . str_replace("'","''",$theText) . "%')";
		if(!$this->dbc->sql_query($sql)){
		$sqlerror=$this->dbc->sql_error();
		die($sqlerror['message']);
		};
		$counter=0;
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
		$retVal[$counter]->Node=$row[$SiteTree->cKeyField];
		$retVal[$counter]->LinkName='';
		$retVal[$counter]->ResultPreview=$textPreview;
		$retVal[$counter]->QSParams='';
		$counter++;
		};
		return $retVal;
	}

	function MakeParamsOuput($theFormPrefix){
		global $HTTP_POST_VARS, $HTTP_POST_FILES, $uploadedfilesdir, $doc_root;
		$PageSize=$this->prms['AdminPageSize']->Value;
		$PageNum=($HTTP_POST_VARS['page']>0)?$HTTP_POST_VARS['page']:1;
		$retVal=$this->ClientScript(0, $theFormPrefix, $PageNum);
		$action=($HTTP_POST_VARS['mod_' . $this->name . '_action'])?$HTTP_POST_VARS['mod_' . $this->name . '_action']:'';
		$memberid=($HTTP_POST_VARS['member'])?$HTTP_POST_VARS['member']:0;
		if($action=="insert"){
			$sql="insert into `$this->modUsersTable` (`name`) values ('" . $HTTP_POST_VARS["name"] . "')";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror['message']);
			};
			$memberid=$this->dbc->sql_nextid();
		};
		if($action=="update"){
			if($HTTP_POST_VARS["delete"]=="on"){
				$sql="delete from `$this->modUsersTable` where `id`=" . $memberid;
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror['message']);
				};
				$memberid=0;
			}else{
				$enabled=($HTTP_POST_VARS['enabled']=='on')?1:0;
				$sql="update `$this->modUsersTable` set `name`='" . $HTTP_POST_VARS["name"] . "', `email`='" . $HTTP_POST_VARS["email"] . "', `password`='" . $HTTP_POST_VARS["password"] . "', `description`='" . $HTTP_POST_VARS["description"] . "', `enabled`=$enabled where `id`=$memberid";
				if(!$this->dbc->sql_query($sql)){
					$sqlerror=$this->dbc->sql_error();
					die($sqlerror['message']);
				};
			};
		};

		if($memberid>0){
				$sql="select `id`,`name`, `email`, `password`, `description`, `enabled` from `$this->modUsersTable` where `id`=$memberid";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror['message']);
			};
			if($row=$this->dbc->sql_fetchrow()){
				$retVal.=drwTableBegin("100%","");
				$retVal.="<tr><td class=colheader colspan=2>Редактирование информации о пользователе</td></tr>";
				$retVal.="$theFormPrefix<input type=hidden name=mod_" . $this->name . "_action value=update><input type=hidden name=member value=$memberid>";
				$retVal.="<tr><td class=frm_data1 align=right>Отображаемое имя:</td><td class=frm_data1><input type=text class=text name=name value=\"" . CutQuots($row["name"]) . "\" size=40></td></tr>";
				$retVal.="<tr><td class=frm_data2 align=right>Электронная почта:</td><td class=frm_data2><input type=text class=text name=email value=\"" . CutQuots($row["email"]) . "\" size=40></td></tr>";
				$retVal.="<tr><td class=frm_data1 align=right>Пароль:</td><td class=frm_data1><input type=text class=text name=password value=\"" . CutQuots($row["password"]) . "\" size=40></td></tr>";
				$retVal.="<tr><td class=frm_data2 align=right valign=top>Внутренний комментарий:</td><td class=frm_data2><textarea name=description cols=50 rows=4>" . CutQuots($row["description"]) . "</textarea></td></tr>";
				$checked=($row["enabled"]==1)?" checked":"";
				$retVal.="<tr><td class=frm_data1 align=right>Вход разрешён:</td><td class=frm_data1><input type=checkbox name=enabled$checked></td></tr>";
				$retVal.="<tr><td colspan=2 class=frm_data2 align=center><input type=checkbox name=delete>&nbsp;-&nbsp;Удалить&nbsp;&nbsp;<input type=submit class=button value=\"обновить\"></td></tr>";
				$retVal.="</form>";
				$retVal.=drwTableEnd();
			};
		};


		$retVal.=drwTableBegin("100%","");
		$retVal.="<tr><td class=colheader colspan=3>Список зарегистрированных пользователей форумов сайта</td></tr>";
		$sql="select `id`, `name`, `email`, `password`, `description`, `enabled` from `$this->modUsersTable` order by `name`";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		};
		$tdclass="data1";
		$counter=0;
		while($row=$this->dbc->sql_fetchrow()){
			$counter++;
			if(($counter>(($PageNum-1)*$PageSize))&&($counter<=(($PageNum)*$PageSize))){
				$retVal.="$theFormPrefix<input type=hidden name=member value=" . $row["id"] . "><tr><td width=2 class=$tdclass>&nbsp;" . CutQuots($row["name"]) . "&nbsp;</td><td width=100% class=$tdclass>" . CutQuots($row["email"]) . "</td><td class=$tdclass align=center><input type=submit class=button value=\"редактировать\"></td></tr></form>";
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
					$PagerStr.='<a style="color:gray;text-decoration:none;">' . $fcounter . '</a> ';
				}else{
					$PagerStr.='<a href="javascript:mod_' . $this->name . '_action(\'\',' . $fcounter . ',\'\')">$fcounter</a> ';
				};
			};
			$PagerStr.="</td></tr>" . drwTableEnd() . "<br>";
		};
		$retVal.=$PagerStr;
		
		$retVal.=drwTableBegin("100%","") . $theFormPrefix . '<input type=hidden name=mod_' . $this->name . '_action value=insert>';
		$retVal.="<tr><td class=colheader colspan=3>Добавить нового пользователя</td></tr>";
		$retVal.="<tr><td class=frm_data1>Отображаемое имя:&nbsp;<input type=text class=text name=name size=60></td><td class=frm_data1 align=center><input type=submit class=button value=\"добавить\"></td></tr>";
		$retVal.="</form>" . drwTableEnd() . "<br>";
		return $retVal;
	}

	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->modParamsTable`;
			CREATE TABLE `$this->modParamsTable` (
				`node` INT NOT NULL DEFAULT '0',
				`rtext` INT NOT NULL DEFAULT '0',
				`guest_read` INT NOT NULL DEFAULT '1',
				`guest_write` INT NOT NULL DEFAULT '0'
			);
		DROP TABLE IF EXISTS `$this->modUsersTable`;
		CREATE TABLE  `$this->modUsersTable` (
				`id` INT NOT NULL AUTO_INCREMENT ,
				`name` VARCHAR( 250 ) NOT NULL DEFAULT '',
				`email` VARCHAR( 250 ) NOT NULL DEFAULT '',
				`password` VARCHAR( 250 ) NOT NULL DEFAULT '',
				`description` VARCHAR( 2000 ) NOT NULL DEFAULT '',
				`enabled` INT NOT NULL DEFAULT 0,
				PRIMARY KEY (  `id` )
				);
		DROP TABLE IF EXISTS `$this->modAccessTable`;
		CREATE TABLE  `$this->modAccessTable` (
				`node` INT NOT NULL ,
				`user` INT NOT NULL ,
				`notify` INT NOT NULL ,
				`moderator` INT NOT NULL
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

$thePhorumModule=new clsPhorumModule('phorum','форум',$db);
$modsArray['phorum']=$thePhorumModule;
?>