<?php

/*
 *OutputKeywords searches for trending keywords and outputs the results
 * @Author Manzoor Ahmed
 */

class OutputKeywords{

var $maxSearch = 5;  //search for only top 5, I just hard coded the value for now, had issues with running query 
var $maxShow =5;    //show only 5 rows 

	function showKeywords($dbc){
	
	$query = $dbc->real_escape_string("SELECT * from `keywords` WHERE `hits` >= 5 GROUP BY `hits`  ASC LIMIT 5;");	
	$query_run = $dbc->query($query) or die($dbc->error);
	
		if($query_run){
		
			echo "<div id='holder'>";
					//print trending course, with image
					echo "<div style='color:green;size:15px;'>";
					echo"<ul style='list-style-type:none;display:inline;'>
						 <li>Trending Keywords</li>
						 </ul>";
					echo "</div>";
					
			while($row = mysqli_fetch_array($query_run)){
					
					echo "<div class ='keyword'>";
					echo"<ul style='list-style-type:none;display:inline;'>
						 <li>$row[1]</li>
						 </ul>";
					echo "</div>";
			}		
		
		}//if
	}//function
	//showKeywords($dbc =$GLOBALS['dbc']);
}//class
?>
