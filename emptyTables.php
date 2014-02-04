<?php

require_once 'connection.php';

/**********************************************************
 *Clean up all tables before crawling
 *emptyTables.php
 *
 *@Author Manzoor Ahmed
 **********************************************************/
 
 /*
 *cleanTables 
 *this function clears all tables from table`sjsucsor_160s1g1`
 */
function cleanTables(){

    //prepare queries for cleaning tables
    $drop1 ="delete from `coursedetails`";
    $drop2 ="delete from `course_data`";
    $drop3 = "delete from `trendingcourses`";

    $dbc=$GLOBALS['dbc'];
    //run queries
    $dbc->query($drop1) or die ($dbc->error);
    $dbc->query($drop2) or die ($dbc->error);
	$dbc->query($drop3) or die($dbc->error);
}

?>
