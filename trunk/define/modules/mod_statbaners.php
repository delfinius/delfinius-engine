<?php

class clsStatbanersModule extends clsStandAloneModule{
	function clsStatbanersModule($modName,$modDName,$dbconnector){
		global $doc_root, $bandir;		
	 	parent::clsStandAloneModule($modName,$modDName,$dbconnector);
		$this->version="1.0.1";
		$this->helpstring="<p>Модуль реализует ротацию банеров на сайте.</p>";
		$this->BanTable="mod_statbaners";
		$this->bandir=$doc_root . $bandir;
		$this->banurl=$bandir;
		$this->baners=array();
		$this->readed=false;
	}


	function GetBaner($needslot = 0){
		$retVal=array();
		if(!$this->readed){
			$sql="select `image`, `color`, `link`, `description`, `active`, `newindow`, `place` from `" . $this->BanTable . "` where `active`=1";
			if(!$this->dbc->sql_query($sql))return $retVal;
			while($row=$this->dbc->sql_fetchrow()){
				$needrow=$row;
				$needrow["fullurl"]=$this->banurl . $needrow["image"];
				$slotid=$needrow["place"];
				if(!isset($this->baners[$slotid]))$this->baners[$slotid]=array();
				$this->baners[$slotid][count($this->baners[$slotid])]=$needrow;
			};
			$this->readed=true;
		};
		$banersforplace=count($this->baners[$needslot]);
		$emptybaner=array("image" => "", "color" => "#AAAAAA", "link" => "", "newindow"=>0, "place"=>0);
		if($banersforplace==0)return $emptybaner;
		$bnum=rand(0,$banersforplace-1);
		$retVal=$this->baners[$needslot][$bnum];
		return $retVal;
	}

	function MakeParamsOuput($theFormPrefix){
		global $HTTP_POST_VARS, $contentscript, $SiteTree, $HTTP_POST_FILES, $doc_root, $bandir;
		$retVal="";
		$need_action="insert";
		$curid=0;
		if($HTTP_POST_VARS["mod_action"]=="insert"){
			$active=($HTTP_POST_VARS["active"]=="on")?1:0;
			$newindow=($HTTP_POST_VARS["newindow"]=="on")?1:0;
			$fulink=($HTTP_POST_VARS["lt"]=="insite")?$HTTP_POST_VARS["insitelink"]:$HTTP_POST_VARS["link"];
			$imagename=$HTTP_POST_VARS["image"];
			if((strlen($imagename)==0)&&($HTTP_POST_FILES["newupload"])){
				$filename=$HTTP_POST_FILES["newupload"]['name'];
				$dotpos = strrpos($filename,'.');
				$fileExt = substr ($filename,$dotpos);
				$imagename=edit_find_freefilename($bandir . "uploaded_", 1, $fileExt);
				if(!copy($HTTP_POST_FILES["newupload"]["tmp_name"], $doc_root . $imagename)){
					$imagename="";
				}else{
					$imagename = addslashes (substr ($imagename,strlen($bandir)));
				};
			};
			$sql="insert into `" . $this->BanTable . "` (`image`, `color`, `link`, `description`, `active`, `newindow`,`place`) values ('$imagename', '" . $HTTP_POST_VARS["color"] . "', '" . $fulink . "','" . $HTTP_POST_VARS["description"] . "',$active,$newindow," . $HTTP_POST_VARS["place"] . ")";
			$this->dbc->sql_query($sql);
		};

		if($HTTP_POST_VARS["mod_action"]=="update"){
			if($HTTP_POST_VARS["delete"]!="on"){
				$active=($HTTP_POST_VARS["active"]=="on")?1:0;
				$newindow=($HTTP_POST_VARS["newindow"]=="on")?1:0;
				$fulink=($HTTP_POST_VARS["lt"]=="insite")?$HTTP_POST_VARS["insitelink"]:$HTTP_POST_VARS["link"];
				$imagename=$HTTP_POST_VARS["image"];
				if((strlen($imagename)==0)&&($HTTP_POST_FILES["newupload"])){
					$filename=$HTTP_POST_FILES["newupload"]['name'];
					$dotpos = strrpos($filename,'.');
					$fileExt = substr ($filename,$dotpos);
					$imagename=edit_find_freefilename($bandir . "uploaded_", 1, $fileExt);
					if(!copy($HTTP_POST_FILES["newupload"]["tmp_name"], $doc_root . $imagename)){
						$imagename="";
					}else{
						$imagename = addslashes (substr ($imagename,strlen($bandir)));
					};
				};
				$sql="update `" . $this->BanTable . "`  set `image`='$imagename', `color`='" . $HTTP_POST_VARS["color"] . "', `link`='" . $fulink . "', `description`='" . $HTTP_POST_VARS["description"] . "', `active`=$active, `newindow`=$newindow, `place`=" . $HTTP_POST_VARS["place"] . " where `id`=" . $HTTP_POST_VARS["id"];
			}else{
				$sql="delete from `" . $this->BanTable . "` where `id`=" . $HTTP_POST_VARS["id"];
			};
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
		};

		if($HTTP_POST_VARS["mod_action"]=="edit"){
			$curid=$HTTP_POST_VARS["id"];
		};
		$bdescription="";
		$bcolor="#808080";
		$bimage="";
		$blink="";
		$bactive="";
		$bnewindow="";
		$place=1;
		if($curid>0){
			$sql="select `id`, `image`, `color`, `link`, `description`, `active`,`newindow`, `place` from `" . $this->BanTable . "` where `id`=$curid";
			if($this->dbc->sql_query($sql)){
				$row=$this->dbc->sql_fetchrow();
				$bdescription=$row["description"];
				$bcolor=$row["color"];
				$bimage=$row["image"];
				$blink=$row["link"];
				$bactive=($row["active"]==1)?" checked":"";
				$bnewindow=($row["newindow"]==1)?" checked":"";
				$place=$row["place"];
				$need_action="update";
			};
		};
		$retVal.=drwTableBegin('100%','');
		$textaction=($need_action=="insert")?"добавить":"изменить";
		$retVal.="<tr><td class=colheader colspan=2>$textaction банер</td></tr>";
		$retVal.="<form method=post enctype=\"multipart/form-data\" name=mod_statbaners_form><input type=hidden name=mod_action value=\"$need_action\"><input type=hidden name=id value=$curid>";
		$retVal.="<tr><td class=data1 align=right>описание:</td><td class=data1><input type=text class=text name=description value=\"" . CutQuots($bdescription) . "\"></td></tr>";
		$insitelink=(strstr($blink,$contentscript));
		$retVal.="<tr><td class=data2 align=right valign=top>ссылка:</td><td class=data2>";
		$selected=($insitelink)?" checked":"";
		$retVal.="<input type=radio name=lt value=insite $selected>&nbsp;<select name=insitelink>";
		$alltree=$SiteTree->GetTree(0,"`visible`=1");
		foreach($alltree as $aNode){
			$curlink="$contentscript?id=" . $aNode['id'];
			$DeepStr="";
			for($laKey2=0;$laKey2<=$aNode["dbs_deep"];$laKey2++)$DeepStr.="&nbsp;&nbsp;";
			$selected=($curlink==$blink)?" selected":"";
			$retVal.="<option value=\"$curlink\" $selected>$DeepStr" . $aNode['name'];
		};
		$retVal.="</select><br>";
		$selected=(!$insitelink)?" checked":"";
		$retVal.="<input type=radio name=lt value=ext $selected>&nbsp;<input type=text class=text name=link value=\"" . CutQuots($blink) . "\"></td></tr>";
		$retVal.="<tr><td class=data1 align=right>цвет фона:</td><td class=data1><input type=text class=text name=color value=\"" . CutQuots($bcolor) . "\"></td></tr>";
		$retVal.="<script>function previewimg(selinp){\ndocument.images['banpreview'].src=(selinp.selectedIndex>0)?'$bandir'+selinp.options[selinp.selectedIndex].value:'/format.gif'\n};</script>";
		$retVal.="<tr><td class=data2 align=right valign=top>картинка:</td><td class=data2><select name=image onchange=\"previewimg(this);\"><option value=\"\">--загрузить новую--";
		if (is_dir($this->bandir)) {
			if ($dh = opendir($this->bandir)) {
				while (($imgfile = readdir($dh)) !== false) {
					$selected = ($bimage==$imgfile)?' selected':'';
					if(($imgfile!='..')&&($imgfile!='.'))$retVal.="<option$selected value=\"$imgfile\">" . $imgfile;
				}
				closedir($dh);
			};
		};
		$retVal.="</select>";
		$retVal.="<br>";
		$retVal.="новая: <input type=file class=text name=newupload size=40><br><img name=banpreview src=/format.gif border=0>";
		$retVal.="</td></tr>";
		$retVal.="<script>previewimg(document.forms['mod_statbaners_form'].image);</script>";
		$retVal.="<tr><td class=data1 align=right>активность:</td><td class=data1><input type=checkbox name=active $bactive></td></tr>";
		$retVal.="<tr><td class=data2 align=right>новое окно:</td><td class=data2><input type=checkbox name=newindow $bnewindow></td></tr>";
		$retVal.="<tr><td class=data1 align=right>№ слота для вывода:</td><td class=data1><input type=text class=text name=place value=\"" . $place . "\"></td></tr>";
		$deletechb=($need_action=="update")?"<input type=checkbox name=delete> - удалить":"";
		$retVal.="<tr><td class=data1 align=center colspan=2>$deletechb<input type=submit class=button value=\"$textaction\"></td></tr>";
		$retVal.="</form>";
		$retVal.=drwTableEnd();
		$retVal.="<br>";
		$retVal.=drwTableBegin('100%','');
		$retVal.="<tr><td class=colheader colspan=4>существующие банеры</td></tr>";
		$sql="select `id`, `image`, `color`, `link`, `description`, `active`, `place` from `" . $this->BanTable . "` order by `id` desc";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror["message"]);
		};
		$tdclass="data1";
		while($row=$this->dbc->sql_fetchrow()){
			$thestyle=($row["active"]==1)?"background-color:green;":"background-color:red;";
			$retVal.="<form method=post><input type=hidden name=mod_action value=edit><input type=hidden name=id value=" . $row["id"] . ">";
			$retVal.="<tr><td class=$tdclass style=\"" . $thestyle . "\">&nbsp;</td>";
			$retVal.="<td class=$tdclass>" . CutQuots($row["description"]) . "</td>";
			$retVal.="<td class=$tdclass>" . CutQuots($row["link"]) . "</td>";
			$retVal.="<td class=$tdclass align=center><input type=submit class=button value=\"редактировать\"></td>";
			$retVal.="</tr></form>";
			$tdclass=($tdclass=="data1")?"data2":"data1";
		};
		$retVal.=drwTableEnd();
		return $retVal;
	}

	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->BanTable`;
			CREATE TABLE `$this->BanTable` (
			  `id` int(11) NOT NULL auto_increment,
			  `image` varchar(250) NOT NULL default '',
			  `color` varchar(50) NOT NULL default '',
			  `link` varchar(250) NOT NULL default '',
			  `description` varchar(250) NOT NULL default '',
			  `active` int(11) NOT NULL default '0',
			  `newindow` int(11) NOT NULL default '0',
			  `place` int(11) NOT NULL default '0',
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

$theStatbanersModule=new clsStatbanersModule("statbaners","статические банеры",$db);
$SAmodsArray["statbaners"]=$theStatbanersModule;
?>