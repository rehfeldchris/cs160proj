<?php

/**
 * Connection class to connect to the database
 * @author Manzoor Ahmed
 */
 
 
 
 $flagLocalDB=false;// set to use local database
 
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



//echo $json_s['Localdbs']['User'];

 
 
 /*
CONNECTION FOR LOCALHOST

define('HOST','localhost');
define('NAME','');
define('PASSWORD','');       
define('DATABASE','sjsucsor_160s1g1');  


CONNECTION FOR REMOTE SERVER

define('HOST','cs160.sjsu-cs.org');  
define('NAME','sec1group1@sjsu-cs.org');
define('PASSWORD','cs160group1');
define('DATABASE','sjsucsor_160s1g1');
*/
//database connection 
global $dbc;
$dbc = mysqli_connect(HOST,NAME,PASSWORD,DATABASE) or die($dbc->connect_error);

?>
