<?php
setlocale(LC_ALL,"ru_RU.CP1251");
error_reporting  (E_ERROR | E_WARNING | E_PARSE ); // This will NOT report uninitialized variables
set_magic_quotes_runtime(0); // Disable magic_quotes_runtime

if( !get_magic_quotes_gpc() )
{
	if( is_array($HTTP_GET_VARS) )
	{
		while( list($k, $v) = each($HTTP_GET_VARS) )
		{
			if( is_array($HTTP_GET_VARS[$k]) )
			{
				while( list($k2, $v2) = each($HTTP_GET_VARS[$k]) )
				{
					$HTTP_GET_VARS[$k][$k2] = addslashes($v2);
				}
				@reset($HTTP_GET_VARS[$k]);
			}
			else
			{
				$HTTP_GET_VARS[$k] = addslashes($v);
			}
		}
		@reset($HTTP_GET_VARS);
	}

	if( is_array($HTTP_POST_VARS) )
	{
		while( list($k, $v) = each($HTTP_POST_VARS) )
		{
			if( is_array($HTTP_POST_VARS[$k]) )
			{
				while( list($k2, $v2) = each($HTTP_POST_VARS[$k]) )
				{
					$HTTP_POST_VARS[$k][$k2] = addslashes($v2);
				}
				@reset($HTTP_POST_VARS[$k]);
			}
			else
			{
				$HTTP_POST_VARS[$k] = addslashes($v);
			}
		}
		@reset($HTTP_POST_VARS);
	}

	if( is_array($HTTP_COOKIE_VARS) )
	{
		while( list($k, $v) = each($HTTP_COOKIE_VARS) )
		{
			if( is_array($HTTP_COOKIE_VARS[$k]) )
			{
				while( list($k2, $v2) = each($HTTP_COOKIE_VARS[$k]) )
				{
					$HTTP_COOKIE_VARS[$k][$k2] = addslashes($v2);
				}
				@reset($HTTP_COOKIE_VARS[$k]);
			}
			else
			{
				$HTTP_COOKIE_VARS[$k] = addslashes($v);
			}
		}
		@reset($HTTP_COOKIE_VARS);
	}
};

if(!defined("SITE_ROOT")){
	define("SITE_ROOT",true);
	$site_root="";
};

$client_ip = ( !empty($HTTP_SERVER_VARS['REMOTE_ADDR']) ) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ( ( !empty($HTTP_ENV_VARS['REMOTE_ADDR']) ) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR );
$client_referer = ( !empty($HTTP_SERVER_VARS['HTTP_REFERER']) ) ? $HTTP_SERVER_VARS['HTTP_REFERER'] : ( ( !empty($HTTP_ENV_VARS['HTTP_REFERER']) ) ? $HTTP_ENV_VARS['HTTP_REFERER'] : $HTTP_REFERER );
$doc_root = ( !empty($HTTP_SERVER_VARS['DOCUMENT_ROOT']) ) ? $HTTP_SERVER_VARS['DOCUMENT_ROOT'] : ( ( !empty($HTTP_ENV_VARS['DOCUMENT_ROOT']) ) ? $HTTP_ENV_VARS['DOCUMENT_ROOT'] : $DOCUMENT_ROOT );

include($site_root . './define/const.php');
include($site_root . './define/db.php');
include($site_root . './define/functions.php');
include($site_root . './define/config.php');
include($site_root . './define/sessions.php');
include($site_root . './define/permitions.php');
include($site_root . './define/editor_serverside.php');
include($site_root . './define/stattexts.php');
include($site_root . './define/db-tree.php');
include($site_root . './define/slots-define.php');
include($site_root . './define/mods.php');

if(!$coreParams["installed"]->Value){
	if(!defined("INSTALL_PROCESS"))header ("Location: /admin/install.php");
};

$contentimage1="";

$NodePath=array();

$hrefSuffix='';
if($coreParams["anticache"]->Value){
	$rnd=rand(1,1000);
	$hrefSuffix.="&rnd=" . $rnd;
};

?>
