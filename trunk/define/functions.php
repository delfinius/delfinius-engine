<?php

$month = array(1=>"январь",2=>"февраль",3=>"март",4=>"апрель",5=>"май",6=>"июнь",7=>"июль",8=>"август",9=>"сентябрь",10=>"октябрь",11=>"ноябрь",12=>"декабрь");
$monthr = array(1=>"января",2=>"февраля",3=>"марта",4=>"апреля",5=>"мая",6=>"июня",7=>"июля",8=>"августа",9=>"сентября",10=>"октября",11=>"ноября",12=>"декабря");

function CutQuots($qStr){
    $rep_symbols=array("/&/","/[\"]/","/</");
    $rep_results=array("&amp;","&quot;","&lt;");
    return preg_replace($rep_symbols, $rep_results, $qStr);
};

//----------- Прорисовка границ таблицы

function drwTableBegin($aWidth=0,$aHeight=0){
	$strWidth=($aWidth==0)?"":" width=" . $aWidth;
	$strHeight=($aHeight==0)?"":" height=" . $aHeight;
	$retval='<table' . $strWidth . $strHeight . ' border=0 cellpadding=0 cellspacing=0><tr><td class=border><table cellpadding=1 cellspacing=1' . $strWidth . $strHeight . '>';
	return $retval;
};

function drwTableEnd(){
	$retval='</table></td></tr></table>';
	return $retval;
};

function fmtFloat($theNumber){
	$dddNumb=$theNumber;
	if(!($dddNumb>0))$dddNumb=0;
	$retValue=round(theNumber*100)/100;
	$dotPos=strpos($retValue,'.');
	if($dotPos==FALSE)$retValue .='.00';
	if($dotPos==strlen($retValue)-2)$retValue.='0';
	return $retValue;
};

function fmtInt($aNumber){
	$retval=ceil($aNumber);
	return $retval;
};

function DatePicker($dpref,$timestamp){
    global $month;
    $dateArray=getdate($timestamp);
    $retval='';
    $retval.='<select name="' . $dpref . 'date">';
    for($counter=1;$counter<=31;$counter++){
	$selected=($counter==$dateArray['mday'])?' selected':'';
	$retval .= '<option value=' . $counter . $selected . '>' . $counter;
    };
    $retval.='</select>&nbsp;';
    $retval.='<select name="' . $dpref . 'month">';
    for($counter=1;$counter<=12;$counter++){
        $selected=($counter==$dateArray['mon'])?' selected':'';
        $retval.='<option value=' . $counter . $selected . '>' . $month[$counter];
    };
    $retval.='</select>&nbsp;';
    $retval.='<select name="'.$dpref.'year">';
    for($counter=1975;$counter<=2020;$counter++){
	$selected=($counter==$dateArray['year'])?' selected':'';
	$retval.='<option'.$selected . ' value=' . $counter . '>' . $counter;
    };
    $retval.='</select>&nbsp;&nbsp;';
    $retval.='<select name="'.$dpref.'hours">';
    for($counter=0;$counter<=23;$counter++){
	$selected=($counter==$dateArray['hours'])?' selected':'';
	$retval.='<option'.$selected . ' value=' . $counter . '>' . $counter . ":00";
    };
    $retval.='</select>';
    
    return $retval;
};

function DatePickerWOT($dpref,$timestamp){
    global $month;
    $dateArray=getdate($timestamp);
    $retval='';
    $retval.='<select name="' . $dpref . 'date">';
    for($counter=1;$counter<=31;$counter++){
	$selected=($counter==$dateArray['mday'])?' selected':'';
	$retval .= '<option value=' . $counter . $selected . '>' . $counter;
    };
    $retval.='</select>&nbsp;';
    $retval.='<select name="' . $dpref . 'month">';
    for($counter=1;$counter<=12;$counter++){
        $selected=($counter==$dateArray['mon'])?' selected':'';
        $retval.='<option value=' . $counter . $selected . '>' . $month[$counter];
    };
    $retval.='</select>&nbsp;';
    $retval.='<select name="'.$dpref.'year">';
    for($counter=1975;$counter<=2020;$counter++){
	$selected=($counter==$dateArray['year'])?' selected':'';
	$retval.='<option'.$selected . ' value=' . $counter . '>' . $counter;
    };
    $retval.='</select>';
    $retval.='<input type=hidden name=' . $dpref . 'hours value=0>';
    return $retval;
};


function PostToDate($dpref){
    global $HTTP_POST_VARS;
    $hours=(isset($HTTP_POST_VARS[$dpref.'hours']))?$HTTP_POST_VARS[$dpref.'hours']:0;
    $retval=mktime($hours,0,0,$HTTP_POST_VARS[$dpref.'month'],$HTTP_POST_VARS[$dpref.'date'],$HTTP_POST_VARS[$dpref.'year']);
    return $retval;
};

//----------- Вставка ссылок

function InsertReferences($srcLine){
    $amessi=$srcLine;
    $amessi=preg_replace("/(\s)(http\S+)/i","\$1<a target=_blank href=\"\$2\">\$2</a>",$amessi);
    $amessi=preg_replace("/(\s)(www\S+)/i","\$1<a target=_blank href=\"http://\$2\">\$2</a>",$amessi);
    $amessi=preg_replace("/(\s)(ftp\S+)/i","\$1<a target=_blank href=\"ftp://\$2\">\$2</a>",$amessi);
    $amessi=preg_replace("/(\w+(\.)*(-)*\w*@\w+(\.\w+)+)/i","<a href=\"mailto:\$1\">\$1</a>",$amessi);
    return $amessi;
};

function striphtml($srcText){
	$search = array ("'<script[^>]*?>.*?</script>'si",  // Вырезает javaScript
	                 "'<[\/\!]*?[^<>]*?>'si",          // Вырезает HTML-теги
	                 "'^[\s\r\n]+'si",          // Вырезает первые пробелы
	                 "'[\s\r\n]+$'si",          // Вырезает последние пробелы
	                 "'&nbsp;'si",          // Замена html-сущности пробела на пробел
        	         "'([\r\n])[\s]+'");                // Вырезает пробельные символы

	$replace = array ("",
        	         "",
        	         "",
        	         " ",
	                 "\\1");
	$retVal = preg_replace($search, $replace, $srcText);
	return $retVal;
};

function GenerateLinkToNode($aNode,$QSParams=''){
	global $contentscript, $hrefSuffix;
	return $contentscript . "?id=" . $aNode . $QSParams . $hrefSuffix;
};


$dd_natletters_low="abcdefghijklmnopqrstuvwxyzабвгдеёжзийклмнопрстуфхцчшщъыьэюя";
$dd_natletters_upp="ABCDEFGHIJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";

function dd_strtoupper($inp){
	global $dd_natletters_low, $dd_natletters_upp;
	return strtr($inp,$dd_natletters_low,$dd_natletters_upp);
};

function dd_strtolower($inp){
	global $dd_natletters_low, $dd_natletters_upp;
	return strtr($inp,$dd_natletters_low,$dd_natletters_upp);
};

?>