<?php
	include("./define/common.php");
	$StringToSearch=stripslashes($HTTP_GET_VARS['ss']);
	if(strlen($StringToSearch)<3)header("Location: .");
	$searchresults=array();
	foreach($modsArray as $aKey => $aMod){
		if($aMod->SearchAble)$searchresults=array_merge($searchresults,$aMod->SearchString($StringToSearch));
	};

	$node=0;
	$HeaderText="результаты поиска";
	$allTree=$SiteTree->GetTree(0,"visible=1");
	$checkforauth=(isset($SAmodsArray["auth"]))?true:false;
	$authorized=(isset($SAmodsArray["auth"]))?$SAmodsArray["auth"]->authorized:false;
	function CheckNodeInTree($theNodeID){
		global $allTree,$SiteTree,$checkforauth,$authorized;
		foreach($allTree as $aKey => $aNode){
			if($aNode[$SiteTree->cKeyField]==$theNodeID){
				if($checkforauth){
					if($authorized){
						return $aNode[$SiteTree->cNameField];
					}else if($aNode["needlogged"]==0){
						return $aNode[$SiteTree->cNameField];
					}else{
						return "";
					};
				}else return $aNode[$SiteTree->cNameField];
			};
		};
		return "";
	};
	
	$stringsearchres='';
	$SubNodesLinks="";
	$PathLink="РЕЗУЛЬТАТЫ ПОИСКА";
	foreach($searchresults as $aKey => $aResult){
		$NodeName=CheckNodeInTree($aResult->Node);
		if($NodeName!=""){
			$linkname=($aResult->LinkName!="")?$NodeName . " / " . $aResult->LinkName:$NodeName;
			$linkname=CutQuots($linkname);
			$stringsearchres.= "<li><a href=\"$contentscript?id=" . $aResult->Node . $aResult->QSParams . "\">" . $linkname . "</a><br>" . $aResult->ResultPreview . "</li>";
		};
	};
	include("./define/top.php");
	if($stringsearchres!=''){
		echo "<ul>" . $stringsearchres . "</ul>";
	}else{
		echo "<strong>Извините, по вашему запросу ничего не найдено</strong>";
	};
	include("./define/bottom.php");
?>