<?php
	include("common-admin.php");

	$Result='';
	$TheCurrentNode=$HTTP_GET_VARS['node'];
	$CanEditparams=false;
	$CanEditperms=false;
	$CanCreateSub=false;
	$CanEditContent=false;
	$CanCreateSub=false;


	function CheckAccessToNode(){
		global $CanEditparams, $CanEditperms, $CanCreateSub, $CanEditContent, $TheCurrentNode, $CanCreateSub;
		if($TheCurrentNode==0){
			$CanEditparams=false;
			$CanEditperms=false;
			$CanEditContent=false;
		}else{
			$CanEditparams=HaveAccess('bigadmin')||HaveNodeAccess($TheCurrentNode,'paramsaccess');
			$CanEditperms=HaveAccess('accessadmin')||HaveNodeAccess($TheCurrentNode,'permitionsaccess');
			$CanEditContent=HaveAccess('bigadmin')||HaveNodeAccess($TheCurrentNode,'contentaccess');
		};
		$CanCreateSub=HaveAccess('bigadmin')||HaveNodeAccess($TheCurrentNode,'makesubnodesaccess');
	};
	CheckAccessToNode();
	if(($HTTP_POST_VARS['action']=='update')&&$CanEditparams){
		$sql="update `" . $SiteTree->cTreeTable. "` set `" . $SiteTree->cNameField . "`= '" . $HTTP_POST_VARS[$SiteTree->cNameField] . "', `symbol`='" .$HTTP_POST_VARS['symbol'] . "', `icon`='" . $HTTP_POST_VARS['icon'] . "', ";
		$sql.=" `" . $SiteTree->cSortField . "`=" . $HTTP_POST_VARS[$SiteTree->cSortField] . ", `visible`=" . (($HTTP_POST_VARS['visible']=='on')?'1':'0') . ", `ableinmenu`=" . (($HTTP_POST_VARS['ableinmenu']=='on')?'1':'0') . " where `" . $SiteTree->cKeyField . "`=" . $TheCurrentNode;
		$db->sql_query($sql);
		if(isset($SAmodsArray["auth"])){
			//Если прикручен модуль авторизации
			$needlogged=($HTTP_POST_VARS["needlogged"]=="on")?1:0;
			$sql="update `" . $SiteTree->cTreeTable. "` set `needlogged`=$needlogged where `" . $SiteTree->cKeyField . "`=" . $TheCurrentNode;
			$db->sql_query($sql);
			$sql="select `needlogged`, `nltext` from `" . $SiteTree->cTreeTable. "` where `" . $SiteTree->cKeyField . "`=" . $TheCurrentNode;
			$db->sql_query($sql);
			$row=$db->sql_fetchrow();
			if(($row["needlogged"]==1)&&($row["nltext"]==0)){
				$nltext=text_create_new();
				$sql="update `" . $SiteTree->cTreeTable. "` set `nltext`=$nltext where `" . $SiteTree->cKeyField . "`=" . $TheCurrentNode;
				$db->sql_query($sql);
			};
		};
		$Result='Информация о разделе изменена.';
	};


	if(($HTTP_POST_VARS['action']=='delete')&&$CanEditparams){
		if($HTTP_POST_VARS['delete']=='on'){
			$SubNodesForDelete=$SiteTree->GetTree($TheCurrentNode);
			$CanDeleteSubNodes=true;
			$SubNodesCounter=0;
			foreach($SubNodesForDelete as $aKey => $sNode){
				$SubNodesCounter++;
				$CanDeleteSubNodes=$CanDeleteSubNodes&&(HaveAccess('bigadmin')||HaveNodeAccess($sNode[$SiteTree->cKeyField],'paramsaccess'));
			};
			if($SubNodesCounter>0)$CanDeleteSubNodes=$CanDeleteSubNodes&&($HTTP_POST_VARS['recur']=='on');
			if(!$CanDeleteSubNodes){
				$Result='Раздел НЕ удалён. Имеются дочерние разделы. Если было запрошено рекурсивное удаление, то на удаление некоторых из них у вас нет доступа.';
			}else{
				$sql="select `" . $SiteTree->cParentField . "` from `" . $SiteTree->cTreeTable . "` where `" . $SiteTree->cKeyField . "`=" . $TheCurrentNode;
				$db->sql_query($sql);
				$TheParentNode=$db->sql_fetchfield($SiteTree->cParentField);
				if(!$coreParams['fulldeletenode']->Value){
					$db->sql_query("update `" . $SiteTree->cTreeTable . "` set `" . $SiteTree->cParentField . "`=(-1) where `" . $SiteTree->cKeyField . "`=" . $TheCurrentNode);
				}else{
					foreach($SubNodesForDelete as $aKey => $aNode){
						if($modsArray[$aNode['type']])
							$modsArray[$aNode['type']]->DeleteStructures($aNode[$SiteTree->cKeyField]);
						$sql="delete from `" . $SiteTree->cTreeTable . "` where `" . $SiteTree->cKeyField . "`=" . $aNode[$SiteTree->cKeyField];
						$db->sql_query($sql);
					};
					$db->sql_query("delete from `" . $SiteTree->cTreeTable . "` where `" . $SiteTree->cKeyField . "`=" . $TheCurrentNode);
				};
				$Result='Раздел удалён.';
				$TheCurrentNode=$TheParentNode;
				CheckAccessToNode();
			};
		}else{
			$Result='Раздел НЕ удалён.';
		};
	};

	if(($HTTP_POST_VARS['action']=='createsubnode')&&$CanCreateSub){
		$sql="insert into `" . $SiteTree->cTreeTable . "` (`" . $SiteTree->cParentField . "`, `" . $SiteTree->cNameField . "`, `" . $SiteTree->cSortField . "`, `type`) values (" . $TheCurrentNode . ",'" . $HTTP_POST_VARS[$SiteTree->cNameField] . "', '" . $HTTP_POST_VARS[$SiteTree->cSortField] . "', '" . $HTTP_POST_VARS['nodetype'] . "')";
		if(!$db->sql_query($sql)){
		    $sqlerror=$db->sql_error();
		    die($sqlerror['message']);
		};
		$TheParentNode=$TheCurrentNode;
		$TheCurrentNode=$db->sql_nextid();
		
/*
		Данный кусок кода копировал в дочерний раздел только права доступа создающего пользователя
		Тот код который написан ниже - копирует все права из родительского раздела
		if(!HaveAccess('bigadmin')){
			$fldNames='';
			$fldParms='';
			foreach($NodeAccessFields as $aKey => $aAccessField){
				$fldNames.=', `' . $aKey . '`';
				$fldParms.=', ' . (HaveNodeAccess($TheParentNode,$aKey)?'1':'0');
			};
			$sql="insert into `cross-moderators` (`node` ,`moderator` ,$fldNames) values ($TheCurrentNode, " . $SessionSettings['id'] . $fldParms . ")";
			$db->sql_query($sql);
		};
*/		
		$sql="insert into `cross-moderators` (`node` ,`moderator` , `contentaccess`, `paramsaccess`, `permitionsaccess`, `makesubnodesaccess`) select $TheCurrentNode, `moderator` , `contentaccess`, `paramsaccess`, `permitionsaccess`, `makesubnodesaccess` from `cross-moderators` where `node`=$TheParentNode";
		$db->sql_query($sql);
		$modsArray[$HTTP_POST_VARS['nodetype']]->CreateStructures($TheCurrentNode);
		CheckAccessToNode();
		$Result='Раздел создан.';
	};


	if(($HTTP_POST_VARS['action']=='movenode')&&HaveAccess('bigadmin')){
		$sql="update `" . $SiteTree->cTreeTable . "` set `" . $SiteTree->cParentField . "`=" . $HTTP_POST_VARS['dest'] . " where `" . $SiteTree->cKeyField . "`=" . $TheCurrentNode;
		$db->sql_query($sql);
		$Result='Раздел перемещён.';
	};

	if(($HTTP_POST_VARS['action']=='changeaccess')&&$CanEditperms){
		$UsersSount=$HTTP_POST_VARS['sumcount'];
		for($counter=1;$counter<=$UsersSount;$counter++){
			$sql="select `node` from `cross-moderators` where `moderator`=" . $HTTP_POST_VARS['moderator' . $counter] . " and `node`=" . $TheCurrentNode;
			if(!$db->sql_query($sql)){
			    $sqlerror=$db->sql_error();
			    die($sqlerror['message']);
			};
			if($db->sql_numrows()==0){
				$InsetFields="";
				$InsetFieldParams="";
				foreach($NodeAccessFields as $aKey => $aAccessField){
				    $InsetFields.=", `$aKey`";
				    $InsetFieldParams.="," . (($HTTP_POST_VARS[$aKey . $counter]=='on')?'1':'0');
				};
				$db->sql_query("insert into `cross-moderators` (`moderator`, `node` $InsetFields) values (" . $HTTP_POST_VARS['moderator' . $counter] . "," . $TheCurrentNode . $InsetFieldParams . ")");
			}else{
				$UpdateFields="";
				foreach($NodeAccessFields as $aKey => $aAccessField){
				    $UpdateFields.="`$aKey` = " . (($HTTP_POST_VARS[$aKey . $counter]=='on')?'1':'0') . " ,";
				};
				$UpdateFields=substr($UpdateFields,0,strlen($UpdateFields)-1);
				$db->sql_query("update `cross-moderators` set $UpdateFields where `moderator`=" . $HTTP_POST_VARS['moderator' . $counter] . " and `node`=" . $TheCurrentNode);
			};
			CheckAccessToNode();
		};
		$Result='Права доступа к разделу изменены.';
	};


	$NodePath=$SiteTree->GetNodePath($TheCurrentNode,false);
	$ThePath='/ ';
	$NodeInfo=array();
	foreach($NodePath as $theNode){
		$ThePath.=$theNode[$SiteTree->cNameField] . ' / ';
		$NodeInfo=$theNode;
	};

	if(strlen($ThePath)>3)$ThePath=substr($ThePath,0,strlen($ThePath)-3);
	$NodeFormPrefix='<form method=post enctype="multipart/form-data" action="node.php?node=' . $TheCurrentNode . '">';

	if($modsArray[$NodeInfo['type']]){
		$modsArray[$NodeInfo['type']]->prms=MergeConfigs($modsArray[$NodeInfo['type']]->prms,GetConfig(0,$NodeInfo['type']));
	};
	if(($HTTP_POST_VARS['action']=='updateconfigpernode')&&HaveAccess('params')&&($TheCurrentNode!=0)&&($modsArray[$NodeInfo['type']])){
		$NeedToSaveConfig=ConfigFromCollection($HTTP_POST_VARS);
		$NeedToSaveConfig=MergeConfigs($modsArray[$NodeInfo['type']]->prms,$NeedToSaveConfig);
		SaveConfig($TheCurrentNode,$NodeInfo['type'],$NeedToSaveConfig);
	};
	if($modsArray[$NodeInfo['type']]){
		$modsArray[$NodeInfo['type']]->prms=MergeConfigs($modsArray[$NodeInfo['type']]->prms,GetConfig($NodeInfo[$SiteTree->cKeyField],$NodeInfo['type']));
	};

    include ("top.php");
    include ($site_root . './define/configformchecker.php');
?>
<script>
var symboldir = "<?php echo $symboldir?>";
var menuicondir = "<?php echo $menuicondir?>";
function changeSymbol(theSelect){
	theSrc=theSelect.options[theSelect.selectedIndex].value;
	if(theSrc!=''){
		document.images['symbolprev'].src=symboldir+theSrc;
	}else{
		document.images['symbolprev'].src='/format.gif';
	};

};

function changeIcon(theSelect){
	theSrc=theSelect.options[theSelect.selectedIndex].value;
	if(theSrc!=''){
		document.images['iconprev'].src=menuicondir+theSrc;
	}else{
		document.images['iconprev'].src='/format.gif';
	};
};

function toauth_edittext(theTextID){
	self.showModalDialog("post-dialog.php?text="+theTextID,"","center:yes;edge:rized;resizable:no;scroll:no;help:no;status:no;unadorned:yes;dialogWidth:720px;dialogHeight:600px");
};

</script>
<center><span style="color:red"><?php echo $Result?></span></center>
<?php echo drwTableBegin('100%',0)?>
<tr><td class=header colspan=2 align=left>раздел: <?php echo $ThePath?></td></tr>
<?php
	if($CanEditparams){
		echo $NodeFormPrefix . '<input type=hidden name=action value=update>';
		if($SessionSettings['suppresshelp']!=1) echo "<tr><td class=data1 colspan=2><p class=main>Вы можете изменить имя раздела, сортировку и доступность раздела на сайте. Сортировка разедлов внутри родительского раздела происходит по специальному полю \"сортировка\" в порядке возрастания.</p></td></tr>";
		echo '<tr><td class=data2 align=right>Имя раздела:</td><td class=data2><input type=text class=text size=40 name="' . $SiteTree->cNameField . '" value="' . CutQuots($NodeInfo[$SiteTree->cNameField]) . '"></td></tr>';
		echo '<tr><td class=data1 align=right>Сортировка:</td><td class=data1><input type=text class=text size=40 name="' . $SiteTree->cSortField . '" value="' . $NodeInfo[$SiteTree->cSortField] . '"></td></tr>';
		$checked=($NodeInfo['visible']==1)?' checked':'';
		echo "<tr><td class=data2 align=right>Доступность:</td><td class=data2><input type=checkbox name=visible $checked> - общий доступ к разделу";
		$checked=($NodeInfo['ableinmenu']==1)?' checked':'';
		echo "<br><input type=checkbox name=ableinmenu $checked> - ссылка в меню";
		echo "</td></tr>";
		if($devprms["usemenusymbols"]){
			echo '<tr><td class=data1 align=right>Рисунок:</td><td class=data1><select name=symbol onchange="changeSymbol(this);"><option value="">--отсутствует--';
			if (is_dir($doc_root . $symboldir)) {
				if ($dh = opendir($doc_root . $symboldir)) {
					while (($imgfile = readdir($dh)) !== false) {
						$selected = ($NodeInfo['symbol']==$imgfile)?' selected':'';
						if(($imgfile!='..')&&($imgfile!='.'))echo "<option$selected value=\"$imgfile\">" . $imgfile;
					}
					closedir($dh);
				};
			};
			if($NodeInfo['symbol']!=''){
				$symbolsrc=$symboldir . $NodeInfo['symbol'];
			}else{
				$symbolsrc='/format.gif';
			};
			echo '</td></tr>';
			echo '<tr><td class=data2 align=right>&nbsp;</td><td class=data2><img width=300 name=symbolprev src="' . $symbolsrc . '" border=0></td></tr>';
		};

		if($devprms["usemenuicons"]){
			echo '<tr><td class=data1 align=right>Иконка в меню:</td><td class=data1><select name=icon onchange="changeIcon(this);"><option value="">--отсутствует--';
			if (is_dir($doc_root . $menuicondir)) {
				if ($dh = opendir($doc_root . $menuicondir)) {
					while (($imgfile = readdir($dh)) !== false) {
						$selected = ($NodeInfo['icon']==$imgfile)?' selected':'';
						if(($imgfile!='..')&&($imgfile!='.'))echo "<option$selected value=\"$imgfile\">" . $imgfile;
					}
					closedir($dh);
				};
			};
			if($NodeInfo['icon']!=''){
				$iconsrc=$menuicondir . $NodeInfo['icon'];
			}else{
				$iconsrc='/format.gif';
			};
			echo '</td></tr>';
			echo '<tr><td class=data2 align=right>&nbsp;</td><td class=data2><img name=iconprev src="' . $iconsrc . '" border=0></td></tr>';
		};


		if(isset($SAmodsArray["auth"])){
			//Если прикручен модуль авторизации
			$checked=($NodeInfo["needlogged"]==1)?" checked":"";
			echo "<tr><td class=data1 align=right>Только авторизованые:</td><td class=data1><input type=checkbox name=needlogged" . $checked . "></td></tr>";
			$disabled=(($NodeInfo["nltext"]==0)||($NodeInfo["needlogged"]==0))?" disabled":"";
			echo "<tr><td class=data2 align=right>Текст для неавторизованых:</td><td class=data2><input $disabled type=button class=button value=\"редактировать текст\" onclick=\"toauth_edittext(" . $NodeInfo["nltext"] . ")\"></td></tr>";

		}

		echo '<tr><td class=data1 align=center colspan=2><input type=submit class=button value="обновить"></td></tr>';
		echo '</form>';
	};

    echo drwTableEnd();

	if($CanEditContent){
		if($modsArray[$NodeInfo['type']]){
			$AdminOutput=$modsArray[$NodeInfo['type']]->MakeAdminOuput($TheCurrentNode, $NodeFormPrefix, $SessionSettings);
			if($AdminOutput!=''){
				echo '<br>' . drwTableBegin('100%',0) . '<tr><td class=header align=left>содержание, тип раздела: "' . $modsArray[$NodeInfo['type']]->realname . '"</td></tr><tr><td class=data1>';
				echo $AdminOutput . '</td></tr>' . drwTableEnd();
			};
		};
	};

	if($CanEditperms){
		$FieldsCount=count($NodeAccessFields)+1;
		echo '<br>' . drwTableBegin('100%',0);
		echo $NodeFormPrefix . '<input type=hidden name=action value=changeaccess><tr><td class=header align=left colspan=' . $FieldsCount . '>изменение прав доступа к разделу</td></tr>';
		if($SessionSettings['suppresshelp']!=1)echo '<tr><td class=data1 colspan=' . $FieldsCount . '><p class=main>Здесь Вы можете назначить специальные права доступа на управление разделом. Не имеет смысла давать права доступа модератору имеющему глобальные администраторские права доступа. При создании дочерних разделов права доступа наследуются. При изменении прав доступа к разделу уже содержащему дочерние права доступа к дочерним разделам не переопределяются.</p></td></tr>';
		echo '<tr><td class=colheader>модератор (зелёный фон - администратор)</td>';
		$SelectFields='';
		$cmSelectFields='';
		foreach($NodeAccessFields as $aKey => $aAccessField){
			echo '<td class=colheader align=center>' . $aAccessField . '</td>';
			$SelectFields.=", `$aKey`";
			$cmSelectFields.=", `cm`.`$aKey` as `$aKey`";
		};
		$sql="select `moderators`.`id` as `moderator`, `moderators`.`login` as `login`, `moderators`.`name` as `name`, `moderators`.`permitions` as `permitions` $cmSelectFields from `moderators` left join (select * from `cross-moderators` where `node`=$TheCurrentNode) as `cm` on `moderators`.`id`=`cm`.`moderator`";
		if(!$db->sql_query($sql)){
		    $sqlerror=$db->sql_error();
		    die($sqlerror['message']);
		};
		$counter=0;
		$tdclass='data1';
		while($row=$db->sql_fetchrow()){
			$counter++;
			echo '<input type=hidden name=moderator' . $counter . ' value=' . $row['moderator'] . '>';
			if(UserHaveAccess('bigadmin',$row['permitions'])){
				$tdstyle=" style=\"background-color:lightgreen;\"";
			}else{
				$tdstyle="";
			};
			echo '<tr><td class=' . $tdclass . ' nowrap' . $tdstyle . '>' . CutQuots($row['login']) . ' (' . CutQuots($row['name']) . ')</td>';
			foreach($NodeAccessFields as $aKey => $aAf){
				$checked=($row[$aKey]==1)?' checked':'';
				echo '<td class=' . $tdclass . ' align=center><input type=checkbox name="' . $aKey . $counter . '"' . $checked . '></td>';
			};
			echo '</tr>';
			$tdclass=($tdclass=='data1')?'data2':'data1';
		};

		echo '</tr><input type=hidden name=sumcount value=' . $counter . '><tr><td class=' . $tdclass . ' colspan=' . $FieldsCount . ' align=center><input type=submit class=button value="обновить"></td></tr></form>';
		echo drwTableEnd();
	};

	if($CanCreateSub){
		echo '<br>' . drwTableBegin('100%',0);
		echo $NodeFormPrefix . '<input type=hidden name=action value=createsubnode><tr><td class=header align=left colspan=2>создание дочернего раздела</td></tr>';
		if($SessionSettings['suppresshelp']!=1)echo '<tr><td class=data1 colspan=2><p class=main>Для создания дочернего раздела введите его название, заполните поле сортировки (сортировка по возрастанию внутри родительского раздела), выберите тип создаваемого раздела и нажмите кнопку "создать". Тип определяется модулем, реализующим наполнение раздела и его отображение.</p></td></tr>';
		echo '<tr><td class=data2 align=right>название раздела:</td><td class=data2 align=left><input type=text class=text name="' . $SiteTree->cNameField . '" size=40></td></tr>';
		echo '<tr><td class=data1 align=right>сортировка:</td><td class=data1 align=left><input type=text class=text name="' . $SiteTree->cSortField . '" size=5 value="10"></td></tr>';
		echo '<tr><td class=data2 align=right>тип раздела:</td><td class=data2 align=left><select name=nodetype>';
		foreach($modsArray as $aKey => $aType)echo '<option value="' . CutQuots($aType->name) . '">' . CutQuots($aType->realname);
		echo '</td></tr>';
		echo '<tr><td class=data1 colspan=2 align=center><input type=submit class=button value="создать"></td></tr></form>';
		echo drwTableEnd();
	};

	if(HaveAccess('bigadmin')&&($TheCurrentNode!=0)){
		echo '<br>' . drwTableBegin('100%',0);
		echo $NodeFormPrefix . '<input type=hidden name=action value=movenode>';
		echo '<tr><td class=header colspan=2>переместить раздел</td></tr>';
		if($SessionSettings['suppresshelp']!=1)echo '<tr><td class=data1 colspan=2><p class=main>Вы можете переместить текущий раздел в любой другой раздел сайта как дочерний. Все существующие подразделы будут перенесены автоматически.</p></td></tr>';
		echo '<tr><td class=data2 align=right>место назначения:</td><td class=data2><select name=dest><option value=0>корневой раздел' . $SiteTree->GetTreeAsOptions(0,$TheCurrentNode,$TheCurrentNode) . '</select></td></tr>';
		echo '<tr><td class=data1 align=center colspan=2><input type=submit class=button value="переместить"></td></tr>';
		echo '</form>';
		echo drwTableEnd();
	};

	if(HaveAccess('params')&&($TheCurrentNode!=0)&&($modsArray[$NodeInfo['type']])){
		echo '<br>' . drwTableBegin('100%',0);
		$ModifedNodeFormPrefix=$NodeFormPrefix;
		$ModifedNodeFormPrefix=substr($ModifedNodeFormPrefix,0,strlen($ModifedNodeFormPrefix)-1) . ' onsubmit="return checkParamsForm(this);">';
		echo $ModifedNodeFormPrefix . '<input type=hidden name=action value=updateconfigpernode>';
		echo '<tr><td class=header colspan=4>праметры модуля "' . CutQuots($modsArray[$NodeInfo['type']]->realname) . '" для текущего раздела</td></tr>';
		if($SessionSettings['suppresshelp']!=1)echo '<tr><td class=data1 colspan=4><p class=main>Здесь Вы можете переопределить параметры работы модуля для текущего раздела (переопределённые параметры будут действовать только в рамках текущего раздела не распространяясь на дочерние разделы). Если параметры не будут переопределены, то для работы будут использоваться либо параметры "по умолчанию" либо переопределённые общими настройками модуля.</p></td></tr>';
		echo ConfigToForm($modsArray[$NodeInfo['type']]->prms);
		echo '<tr><td class=data2 align=center colspan=4><input type=submit class=button value="обновить параметры"></td></tr>';
		echo '</form>';
		echo drwTableEnd();
	};

	if($CanEditparams){
		echo '<br>' . drwTableBegin('100%',0);
		echo $NodeFormPrefix . '<input type=hidden name=action value=delete>';
		echo '<tr><td class=header colspan=2>удаление раздела</td></tr>';
		if($SessionSettings['suppresshelp']!=1)echo '<tr><td class=data1 colspan=2><p class=main>Вы можете удалить текущий раздел. Метод удаления ("полное" или "потеря родителя") определяется одним из общих параметров сайта.</p></td></tr>';
		echo '<tr><td class=data2 width=50% align=right>подтверждение удаления:</td><td class=data2><input type=checkbox name=delete></td></tr>';
		echo '<tr><td class=data1 align=right>рекурсивное удаление всех подразделов:</td><td class=data1><input type=checkbox name=recur></td></tr>';
		echo '<tr><td class=data2 align=center colspan=2><input type=submit class=button value="удалить"></td></tr>';
		echo '</form>';
		echo drwTableEnd();
	};
	
	if(($SAmodsArray["counter"])&&($TheCurrentNode!=0)){
		echo "<br>" . drwTableBegin('100%',0);
		echo "<tr><td class=header>Статистика по разделу</td></tr>";
		echo "<tr><td class=data1>" . $SAmodsArray["counter"]->NodeStat($NodeFormPrefix,$TheCurrentNode) . "</td></tr>";
		echo drwTableEnd();
	};
	
    include("bottom.php");
?>