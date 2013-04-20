<?

require_once 'connection.php';

/*
 *this class is used to clean up all tables before crawling
 *emptyTables.php
 *@Author Manzoor Ahmed
 */
 
 /*
 *cleanTables 
 *this function clears all tables from `sjsucsor_160s1g1`
 */
function cleanTables(){

//prepare queries for cleaning tables
$drop1 ="TRUNCATE TABLE `sjsucsor_160s1g1`.`coursedetails`" or die(mysql_error());
$drop2 ="TRUNCATE TABLE `sjsucsor_160s1g1`.`course_data`" or die(mysql_error());

$dbc=$GLOBALS['dbc'];
//clean coursedetails table
$dbc->query($drop1) or die (mysql_error());
//clean course_data table
$dbc->query($drop2) or die (mysql_error());

}

?>
