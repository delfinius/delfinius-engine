<?php

if(!session_id())session_start();
$sSettings="";

$SessionSettings=array();
    
function GetSessionSettings(){
    global $HTTP_SESSION_VARS, $SessionSettings;
    $lSettings=$HTTP_SESSION_VARS['rsSettings'];
    $lines=split(";;",$lSettings);
    foreach($lines as $aKey => $line){
	$splitted=split("==",$line);
	if(count($splitted)>0)$SessionSettings[$splitted[0]]=$splitted[1];
    };
    return;
};


function WriteSessionSettings(){
    global $SessionSettings, $sSettings, $HTTP_SESSION_VARS;
    $lSettings='';
    foreach($SessionSettings as $aKey => $Value){
	$lSettings.=$aKey . "==" . $Value . ";;";
    };
    $sSettings=$lSettings;
//    if(session_is_registered('sSettings'))session_unregister('sSettings');
//    session_register('sSettings');
  $HTTP_SESSION_VARS['rsSettings']=$sSettings;
    return;
};

GetSessionSettings();

?>
