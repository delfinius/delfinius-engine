<?php
class cslSearchResult{
	function cslSearchResult(){
		$this->Node=0;
		$this->LinkName='';
		$this->ResultPreview='';
		$this->QSParams='';
	}
}

class clsModule{
    var $name;
    var $realname;
    var $version = '0';
    var $helpstring = '';
    function clsModule ($modName,$modDName,$dbconnector){
	$this->dbc = $dbconnector;
	$this->name = $modName;
	$this->realname = $modDName;
	$this->version = '0';
	$this->HaveGlobalParams=false;
	$this->prms=array();
	$this->SearchAble=false;
	return $this;
    }
    
	function MakeAdminOuput($theNode, $theFormPrefix){
		return '';
	}

	function MakeUserOuput($theNode, $theFormPrefix){
		return '';
	}

	function MakeParamsOuput($theFormPrefix){
		return '';
	}

	function CreateStructures($theNode){
	}

	function DeleteStructures($theNode){
	}

	function SearchString($theText){
		$retVal=array();
		return $retVal;
	}

	function Install(){
	}

	function MakeSelfHrefParams($theNode){
		global $contentscript,$hrefSuffix;
		$retval="href=\"$contentscript?id=$theNode$hrefSuffix\" target=_self";
		return $retval;
	}
}

class clsStandAloneModule{
	var $name;
	var $realname;
	var $version = '0';
	var $helpstring = '';
	function clsStandAloneModule ($modName,$modDName,$dbconnector){
		$this->dbc = $dbconnector;
		$this->name = $modName;
		$this->realname = $modDName;
		$this->version = '0';
		$this->prms=array();
		return $this;
	}

	function MakeParamsOuput($theFormPrefix){
		return '';
	}
	function MakeAdminOuput($theNode, $theFormPrefix){
		return '';
	}

	function MakeUserOuput($theNode, $theFormPrefix){
		return '';
	}

	function Install(){
	}
}

$SAmodsArray = array();
//include ($site_root . './define/modules/mod_subscribe.php');
include ($site_root . './define/modules/mod_statbaners.php');
//include ($site_root . './define/modules/mod_auth.php');
include ($site_root . './define/modules/mod_contacts.php');
include ($site_root . './define/modules/mod_counter.php');

$modsArray = array();

include ($site_root . './define/modules/mod_text.php');
include ($site_root . './define/modules/mod_news.php');
include ($site_root . './define/modules/mod_redir.php');
include ($site_root . './define/modules/mod_sitemap.php');
include ($site_root . './define/modules/mod_articles.php');
include ($site_root . './define/modules/mod_gb.php');
include ($site_root . './define/modules/mod_forms.php');
include ($site_root . './define/modules/mod_planeta.php');
include ($site_root . './define/modules/mod_gallery.php');
include ($site_root . './define/modules/mod_votes.php');
include ($site_root . './define/modules/mod_subnodes.php');
include ($site_root . './define/modules/mod_invest.php');
include ($site_root . './define/modules/mod_employees.php');
?>