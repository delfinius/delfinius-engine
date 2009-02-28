<?php

switch($dbms)
{
	case 'mysql':
		include($site_root . './define/db/mysql.php');
		break;

	case 'mysql4':
		include($site_root . './define/db/mysql4.php');
		break;

	case 'postgres':
		include($site_root . './define/db/postgres7.php');
		break;

	case 'mssql':
		include($site_root . './define/db/mssql.php');
		break;

	case 'oracle':
		include($site_root . './define/db/oracle.php');
		break;

	case 'msaccess':
		include($site_root . './define/db/msaccess.php');
		break;

	case 'mssql-odbc':
		include($site_root . './define/db/mssql-odbc.php');
		break;
}

// Make the database connection.
$db = new sql_db($dbhost, $dbuser, $dbpasswd, $dbname, false);
if(!$db->db_connect_id)
{
   die("Could not connect to the database");
}

?>