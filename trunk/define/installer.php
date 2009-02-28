<?php
	$alltablescreator="DROP TABLE IF EXISTS `config`;
		CREATE TABLE `config` (
		  `node` int(11) NOT NULL default '0',
		  `mod` varchar(50) NOT NULL default '',
		  `name` varchar(250) NOT NULL default '',
		  `description` varchar(250) NOT NULL default '',
		  `datatype` varchar(50) NOT NULL default '',
		  `int` int(11) NOT NULL default '0',
		  `float` float NOT NULL default '0',
		  `char` varchar(250) NOT NULL default '',
		  `memo` text NULL,
		  `bool` int(11) NOT NULL default '0',
		  `protected` int(11) NOT NULL default '0'
		);
		DROP TABLE IF EXISTS `cross-moderators`;
		CREATE TABLE `cross-moderators` (
		  `node` int(11) NOT NULL default '0',
		  `moderator` int(11) NOT NULL default '0',
		  `contentaccess` int(11) NOT NULL default '0',
		  `paramsaccess` int(11) NOT NULL default '0',
		  `permitionsaccess` int(11) NOT NULL default '0',
		  `makesubnodesaccess` int(11) NOT NULL default '0'
		);
		DROP TABLE IF EXISTS `moderators`;
		CREATE TABLE `moderators` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(250) NOT NULL default '',
		  `login` varchar(250) NOT NULL default '',
		  `password` varchar(50) NOT NULL default '',
		  `permitions` int(11) NOT NULL default '0',
		  `email` varchar(250) NOT NULL default '',
		  `description` varchar(250) NOT NULL default '',
		  `suppresshelp` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		);
		DROP TABLE IF EXISTS `sitetree`;
		CREATE TABLE `sitetree` (
		  `id` int(11) NOT NULL auto_increment,
		  `parent` int(11) NOT NULL default '0',
		  `name` varchar(250) NOT NULL default '',
		  `Visible` int(11) NOT NULL default '0',
		  `type` varchar(50) NOT NULL default '',
		  `Sort` int(11) NOT NULL default '0',
		  `symbol` varchar(50) NOT NULL default '',
		  `icon` varchar(50) NOT NULL default '',
		  `needlogged` int(11) NOT NULL default '0',
		  `nltext` int(11) NOT NULL default '0',
		  `ableinmenu` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		);
		DROP TABLE IF EXISTS `texts`;
		CREATE TABLE `texts` (
		  `id` int(11) NOT NULL auto_increment,
		  `text` text,
		  PRIMARY KEY  (`id`)
		);
		DROP TABLE IF EXISTS `texts_atts`;
		CREATE TABLE `texts_atts` (
		  `text` int(11) NOT NULL default '0',
		  `filename` varchar(250) NOT NULL default '',
		  `originalname` varchar(250) NOT NULL default ''
		);
		delete from `moderators` where login='Delfin';
		insert into `moderators` (`name`, `login`, `password`, `permitions`, `email`, `description`) values ('Дёмин Алексей', 'Delfin', 'e0f14e97ff949f7e079aab0b61221f3b', 32895, 'delfin@extrim.it', 'Разработчик');
		";
		
	function InstallAll($reinstall=false){
		global $db, $alltablescreator, $modsArray, $SAmodsArray;
		$splitedinstructions=split(";",$alltablescreator);
		foreach($splitedinstructions as $oneinstruction)
			if($reinstall){
				$db->sql_query($oneinstruction);
			}else if(strstr($oneinstruction,"DROP TABLE")==FALSE){
					$db->sql_query($oneinstruction);
				};
		foreach($modsArray as $oneMod)
			$oneMod->Install($reinstall);
		foreach($SAmodsArray as $oneMod)
			$oneMod->Install($reinstall);
			
		$setflagsql="delete from `config` where `node`=0 and `mod`='core' and `name`='installed'";
		$db->sql_query($setflagsql);
		$setflagsql="insert into `config` (`node`, `mod`, `name`, `description`, `datatype`, `bool`) values (0,'core','installed','Флаг установленности сайта (наличие необходимых структур в БД).', 'bool', 1)";
		$db->sql_query($setflagsql);
	};
?>