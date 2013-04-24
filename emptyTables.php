<?php

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
    $drop1 ="delete from `sjsucsor_160s1g1`.`coursedetails`";
    $drop2 ="delete from  `sjsucsor_160s1g1`.`course_data`";

    $dbc=$GLOBALS['dbc'];
    //clean coursedetails table
    $dbc->query($drop1) or die ($dbc->error);
    //clean course_data table
    $dbc->query($drop2) or die ($dbc->error);
}
?>
