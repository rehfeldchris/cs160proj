<?php

/*
 * Connection class to connect to the database
 * @author Manzoor Ahmed
 */
 
//CONNECTION FOR LOCALHOST

define('HOST','localhost');
define('NAME','root');
define('PASSWORD','');       
define('DATABASE','sjsucsor_160s1g1');  

//CONNECTION FOR REMOTE SERVER

//define('HOST','cs160.sjsu-cs.org');  
//define('NAME','sec1group1@sjsu-cs.org');
//define('PASSWORD','cs160group1');
//define('DATABASE','sjsucsor_160s1g1');

//database connection 
$dbc = mysqli_connect(HOST,NAME,PASSWORD,DATABASE) or die(mysqli_connect_error());

?>
