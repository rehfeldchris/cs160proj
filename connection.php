<?php

/**
 * Connection class to connect to the database
 * @author Manzoor Ahmed
 */
 
 
 
$flagLocalDB = false;
 
$string = file_get_contents("dbconfiguration.json");
$json_s = json_decode($string,true);
if($flagLocalDB)// assumes local connection
{
	define('HOST',$json_s['Localdbs']['Host']);
	define('NAME',$json_s['Localdbs']['User']);
	define('PASSWORD',$json_s['Localdbs']['Password']);       
	define('DATABASE',$json_s['Localdbs']['Database']); 
}

else// assumes remote connection
{
	define('HOST',$json_s['Remotedbs']['Host']);
	define('NAME',$json_s['Remotedbs']['User']);
	define('PASSWORD',$json_s['Remotedbs']['Password']);       
	define('DATABASE',$json_s['Remotedbs']['Database']); 
}

//database connection 

global $dbc;
$dbc = mysqli_connect(HOST,NAME,PASSWORD,DATABASE) or die($dbc->connect_error);
$dbc->set_charset('utf8');
?>
