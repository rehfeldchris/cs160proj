<?php

/**
 *ShowTrendingCourses.php outputs trending courses
 *
 *@Author Manzoor Ahmed
 *TODO: I will make this a class, and then just call the method, but for now it shows trending courses
 */
 
include 'connection.php';

//we need at least five hits for a course to be considered it as a trending course; we can always change this.  
global $minHits;
//how many trending courses we need to show--$limit is the max number of courses we can show
global $limit;

function showTrendingCourses(){

//grab global connection
$dbc = $GLOBALS['dbc'];

$minHits=5;  // to consider a course as a trending
$limit=5;    //how many courses to show

//show only five records from trending courses with higher hits
$query = $dbc->real_escape_string("SELECT `id` from `trendingcourses` WHERE `hits` >= $minHits ORDER BY `hits` ASC LIMIT 5;");
//run query
$maxHits = $dbc->query($query) or die($dbc->error);
	
	if($maxHits){
	
		while($row = mysqli_fetch_array($maxHits)){
		
			//need id first to find course information
			$id = $row['id'];
			
			//need to find the title with given $id
			$title_query = $dbc->real_escape_string("SELECT `title` from `course_data` WHERE `id` = $id;");
			$results = $dbc->query($title_query) or die($dbc->error);
			
			if($results){
				//get the entire row
				$title = $results->fetch_row();
				//get the title, and covert it to link so user can click on it
				$course_title = $title[0];
		
				//need to get course link to course
				$link_query = $dbc->real_escape_string("SELECT `course_link` from `course_data` WHERE `id` = $id;");
				$link_query_run = $dbc->query($link_query) or die($dbc->error);
				$link;
				//if no empty $link
				if($link_query_run){
					$col = $link_query_run->fetch_row();
					//get the link 
					$link = $col[0];
				}//if 
				
				else{
					//if course with no link
					$link ="#";
				}//else
				
				//print trending course, format2
				echo"<ul style='list-style-type:none;display:inline;'><li><a href='$link'>$course_title</a></li></ul>";
			}//result			
			
			else{
			    echo "";
			}			
		}//end while
	}//if
	//we don't have trending courses yet
	else{
		echo "";
	}
}//function

showTrendingCourses();

?>
