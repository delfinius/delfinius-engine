<?php
	include("./define/common.php");
	$node=$HTTP_GET_VARS['id'];
	if(!is_numeric($node))header("Location: .");
	$NodePath=$SiteTree->GetNodePath($node,false);
	if(count($NodePath)==0)header("Location: .");
	$checkforauth=(isset($SAmodsArray["auth"]))?true:false;
	$authorized=(isset($SAmodsArray["auth"]))?$SAmodsArray["auth"]->authorized:false;
	$PathLink="<a class=header href=.>Начало</a>";
	for($counter=0;$counter<(count($NodePath));$counter++){
		if($NodePath[$counter]["visible"]!=1){
			header("Location: .");
			exit();
		};
		$PathLink.=" <img src=simages/arr-3.gif border=0 width=10 height=10 align=absmiddle> ";
		$PathLink.="<a class=header " . $modsArray[$NodePath[$counter]['type']]->MakeSelfHrefParams($NodePath[$counter][$SiteTree->cKeyField]) . ">" . CutQuots($NodePath[$counter][$SiteTree->cNameField]) . "</a>";
	};
	$NodeInfo=$NodePath[count($NodePath)-1];
	$HeaderText=$NodeInfo[$SiteTree->cNameField];

	$haveaccess=(!$checkforauth)||($authorized&&($NodeInfo["needlogged"]==1)||($NodeInfo["needlogged"]==0));

	$SubNodesLinks="";
	if($haveaccess){
		$SubNodes=$SiteTree->GetExpandedLevel($node,"visible=1");
		foreach($SubNodes as $theSubNode){
			$SubNodesLinks.="<a href=\"$contentscript?id=" . $theSubNode[$SiteTree->cKeyField] . $hrefSuffix . "\">" . CutQuots($theSubNode[$SiteTree->cNameField]) . "</a> :: ";
		};
		if(strlen($SubNodesLinks)>0)$SubNodesLinks="&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;" . substr($SubNodesLinks,0,strlen($SubNodesLinks)-3);
	};
	if(count($NodePath)<2)$SubNodesLinks="";
	$modOutPut="";
	if($modsArray[$NodeInfo['type']]){
		if($haveaccess){
			$modsArray[$NodeInfo['type']]->prms=MergeConfigs($modsArray[$NodeInfo['type']]->prms,GetConfig(0,$NodeInfo['type']));
			$modsArray[$NodeInfo['type']]->prms=MergeConfigs($modsArray[$NodeInfo['type']]->prms,GetConfig($NodeInfo[$SiteTree->cKeyField],$NodeInfo['type']));
			$modOutPut=$modsArray[$NodeInfo['type']]->MakeUserOuput($NodeInfo[$SiteTree->cKeyField],"$contentscript?id=" . $node . $hrefSuffix);
		}else{
			$modOutPut=array();
			$modOutPut[0]="<h4>Отсутствуют права доступа</h4>";
			$modOutPut[0].=html_display($NodeInfo["nltext"]);
			$modOutPut[0].=$SAmodsArray["auth"]->DisplayNeedAuthPage(400);
		};
	};

	if($SAmodsArray["counter"])$SAmodsArray["counter"]->WriteLog($node);
	include("./define/top.php");
	include("./define/menu.php");
	if($modsArray[$NodeInfo['type']]){
		echo $modOutPut[0];
	};
	include("./define/bottom.php");
?>