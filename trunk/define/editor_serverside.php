<?php

function edit_cutbody($theInputHTML){
	$RetVal=stripslashes($theInputHTML);
	$RetVal=str_replace("<BODY>","<body>",$RetVal);
	$RetVal=str_replace("</BODY>","</body>",$RetVal);
	$bodyBeginTagPos=strpos($RetVal,'<body>');
	$bodyEndTagPos=strpos($RetVal,'</body>');
	if($bodyBeginTagPos==FALSE)return '';
	if($bodyEndTagPos==FALSE)return '';
	$RetVal=substr($RetVal,$bodyBeginTagPos+6,($bodyEndTagPos-$bodyBeginTagPos-6));
	$RetVal=preg_replace("/(\r*\n)+/","\n",$RetVal);
	return $RetVal;
};

function edit_find_freefilename($thePrefix,$theStartCount,$theSufix){
	global $doc_root;
	clearstatcache();
	$theCounter=$theStartCount;
	$tmpFileName=$thePrefix . $theStartCount . $theSufix;
	while(file_exists($doc_root . $tmpFileName)){
		$theCounter++;
		$tmpFileName=$thePrefix . $theCounter . $theSufix;
	};
	return $tmpFileName;
};

function edit_replace_oneimage($cursrc,$filenum,$theTextID){
	global $sipath;
	global $HTTP_POST_FILES;
	global $doc_root;
	if($HTTP_POST_FILES['uploadfile' . $filenum]){
	    $filename=$HTTP_POST_FILES['uploadfile' . $filenum]['name'];
	    $dotpos = strrpos($filename,'.');
	    $fileExt = substr ($filename,$dotpos);
	    $newFileName=edit_find_freefilename($sipath . 'img_txt' . $theTextID . '_', $filenum, $fileExt);
	    if(!copy($HTTP_POST_FILES['uploadfile' . $filenum]['tmp_name'], $doc_root . $newFileName)){
		$newFileName=$sipath . 'blank.gif';
	    };
	}else{
    	    $newFileName=$sipath . 'blank.gif';
	};
	return 'src="' . $newFileName . '"';
}

function edit_replace_images($theInputHTML, $theTextID){
	$re_repimage="/src=\"([^\/][^\"]+)\"/e";
	$RetVal=$theInputHTML;
	$counter=1;
	$RetVal=preg_replace($re_repimage,"edit_replace_oneimage('$1',\$counter++,$theTextID);",$RetVal);
	return $RetVal;
};

function html_display($TheID){
	global $db;
	$sql="select `text` from `texts` where `id`=" . $TheID;
	$db->sql_query($sql);
	if($db->sql_numrows()>0){
		$retVal=$db->sql_fetchfield('text');
		return $retVal;
	}else{
		return 'Чёт нету этого текста...';
	};
};

function text_create_new(){
	global $db;
	$sql='insert into texts (text) values (\'<p>&nbsp;</p>\')';
	if(!$db->sql_query($sql)){
	    $sqlerror=$db->sql_error();
	    die($sqlerror['message']);
	};
	$localTextID=$db->sql_nextid();
	return $localTextID;
};
?>