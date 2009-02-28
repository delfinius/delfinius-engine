<?php

class clsSitemapModule extends clsModule{


	function clsSitemapModule($modName,$modDName,$dbconnector){
	    parent::clsModule($modName,$modDName,$dbconnector);
	    $this->version="1.0.3";
	    $this->helpstring="<p>������ ������������ �� ����������� ������ �����.</p>";
	    $this->prms["welcome"]=new ConfigParam("welcome");
	    $this->prms["welcome"]->Description="������������� ����� ����� ������������ ����� �����";
	    $this->prms["welcome"]->DataType='memo';
	    $this->prms["welcome"]->Value="";
	    
	    $this->prms["template"]=new ConfigParam("template");
	    $this->prms["template"]->Description="������ ����������� ����� �����";
	    $this->prms["template"]->DataType="memo";
	    $this->prms["template"]->Value="<table width=100% height=* border=0 cellpadding=0 cellspacing=0><tr><td>--welcome----lines--</td></tr></table>";
	}

	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
	    $retVal="������ �� ����� ���������������� ��������";
	    return $retVal;
	}

	function MakeUserOuput($theNode, $theFormPrefix){
	    global $SiteTree, $modsArray;
	    $retVal=array();
	    $rvText="";
	    $theTree=$SiteTree->GetTree(0,"visible=1 and ableinmenu=1");
	    $pDeep=0;
	    foreach($theTree as $aKey => $aRec){
		$DeepDiff=$aRec["dbs_deep"]-$pDeep;
		if($DeepDiff>0)for($counter=0;$counter<$DeepDiff;$counter++)
					$rvText.="<ul>";
		if($DeepDiff<0)for($counter=0;$counter>$DeepDiff;$counter--)
					$rvText.="</ul>";
		$pDeep=$aRec["dbs_deep"];
		$rvText.="<li><a " . $modsArray[$aRec["type"]]->MakeSelfHrefParams($aRec[$SiteTree->cKeyField]) . ">" . CutQuots($aRec[$SiteTree->cNameField]) . "</a></li>";
	    };
	    for($counter=0;$counter<$pDeep;$counter++)
		$rvText.="</ul>";
	    $retVal[0]=$this->prms["template"]->Value;
	    $retVal[0]=str_replace("--welcome--",$this->prms["welcome"]->Value,$retVal[0]);
	    $retVal[0]=str_replace("--lines--",$rvText,$retVal[0]);
	    return $retVal;
	}
}

$theSitemapModule=new clsSitemapModule('sitemap','����� �����',$db);
$modsArray['sitemap']=$theSitemapModule;
?>