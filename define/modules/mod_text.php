<?php

class clsTextModule extends clsModule{


	function clsTextModule($modName,$modDName,$dbconnector){
	    parent::clsModule($modName,$modDName,$dbconnector);
	    $this->SearchAble=true;
	    $this->version='1.0.1';
	    $this->helpstring='<p>ћодуль реализует разделы содержащие обычный текст с возможност€ми расширеного форматировани€, вставки картинок и прочего. ѕри просмотре сайта в заголовке окана отображаетс€ название раздела.</p>';

	    $this->prms["template"]=new ConfigParam("template");
	    $this->prms["template"]->Description="Ўаблон вывода текста. ƒопускаемые дл€ замены значени€: text";
	    $this->prms["template"]->DataType="memo";
	    $this->prms["template"]->Value="--text--";
	    $this->prms["template"]->Protected=false;
	    
	    $this->modTable="mod_text";

	}

	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
	    $retVal='';
	    $sql="select rtext from `$this->modTable` where node=$theNode";
	    if(!$this->dbc->sql_query($sql)){
    	        $sqlerror=$this->dbc->sql_error();
		die($sqlerror['message']);
	    };
	    if($this->dbc->sql_numrows()>0){
		$row=$this->dbc->sql_fetchrow();
		$textID=$row['rtext'];
		$retVal.="<iframe name=\"mod_text_editor\" border=0 width=100% height=700 marginwidth=0 marginheight=0 scrolling=yes frameborder=0></iframe>";
		$retVal.="<form method=post action=post.php enctype=\"multipart/form-data\" name=mod_text_go_form target=mod_text_editor><input type=hidden name=textID value=$textID></form>";
		$retVal.="<script>document.forms['mod_text_go_form'].submit();</script>";
	    };
	    return $retVal;
	}

	function MakeUserOuput($theNode, $theFormPrefix){
	    $retVal=array();
	    $sql="select `texts`.`text` as `text` from `texts` inner join `$this->modTable` on `$this->modTable`.`rtext`=`texts`.`id` where `$this->modTable`.`node`=" . $theNode;
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
	    $sql="insert into `$this->modTable` (`node`, `rText`) values ($theNode,$textid)";
	    $this->dbc->sql_query($sql);
	}

	function DeleteStructures($theNode){
	    $sql="delete from `texts` where `id` in (select `rText` from `$this->modTable` where `node`=$theNode)";
	    $this->dbc->sql_query($sql);
	    $sql="delete from `$this->modTable` where `node`=$theNode";
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

	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->modTable`;
			CREATE TABLE `$this->modTable` (
			  `node` int(11) NOT NULL default '0',
			  `rtext` int(11) NOT NULL default '0'
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

$theTextModule=new clsTextModule('text','текст',$db);
$modsArray['text']=$theTextModule;
?>