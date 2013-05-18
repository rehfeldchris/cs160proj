<html>
	<head>
		<title>Trending Courses</title>
		<meta name="Author" content="Manzoor Ahmed"/>

		<style type="text/css">
			
			#holder{
					width:auto;
					height:auto;
					}
			
			.course{
					background: none repeat scroll 0 0  ;
					border: 3px solid #73C0E6;
					border-radius: 10px 10px 10px 30px;
					border-color:grey;
					list-style: none outside none;
					font-size:16px;
					font-weight:bold;
					font-family:Verdana, Arial, Helvetica, sans-serif;
					min-height: 30px;
					overflow: hidden;
					padding: 5px 10px 15px 15px;
					position: relative;
					width: 97%;
					z-index: 3;
					}
			.trend{
					background: none repeat scroll 0 0 #CCCCCC;
					border: 3px solid #73C0E6;
					border-radius: 10px 10px 10px 10px;
					border-color:grey;
					list-style: none outside none;
					font-size:18px;
					font-family:Verdana, Arial, Helvetica, sans-serif;
					font-weight:bold;
					min-height: 20px;
					overflow: hidden;
					padding: 5px 10px 15px 15px;
					position: relative;
					width: 97%;
					z-index: 3;
					}
					
			

		</style>
	
	</head>
<body>

<?php
/**
 *ShowTrendinCourses.php select courses with hits starting from $minHits, and shows only five of them with higer hits
 *
 *@Author Manzoor Ahmed
 * 
 */
 
require_once("connection.php");

//we need at least five hits for a course to consider it as a trending course; we can always change this.  
global $minHits;
//how many trending courses we need to show--$limit is the max number of courses we can show
global $limit;

#we need at least five hits for a course to consider it as a trending course; we can always change this.  
#$minHits;
#how many trending courses we need to show--$limit is the max number of courses we can show

class ShowTrendingCourses{

	function __construct(){
	}
	
	/*
	 *connects to database and prints all the results
	 *$dbc, gobla dbc connection
	 */
	function showTrendingCourses($dbc){
	
	//show only five records from trending courses with higher hits
	$query = "SELECT `id` from `trendingcourses` WHERE `hits` >= 5 ORDER BY `hits` ASC LIMIT 3;";
	//run query
	$maxHits = $dbc->query($query) or die($dbc->error);
		
		if($maxHits){
		
			echo "<div id='holder'>";
			//print trending course, with image
					echo "<div class ='trend'>";
						echo"<ul style='list-style-type:none;display:inline;'>
								<li>Trending Courses</li>
							</ul>";
					echo "</div>";
					
			while($row = mysqli_fetch_array($maxHits)){
			
				//need id first to find course information
				$id = $row['id'];
				
				//need to find the title with given $id
				$course_query = $dbc->real_escape_string("SELECT title, course_link from `course_data` WHERE `id` = $id;");
				$results = $dbc->query($course_query) or die($dbc->error);
				//echo $course_query;
				
				if($results && $results->num_rows){
					//get the entire row
					$row = $results->fetch_array();
					//get the title, and covert it to link so user can click on it
					$course_title = $row['title'];
					$link = $row['course_link'];
					
					//print trending course, with image
					echo "<div class ='course'>";
						echo"<ul style='list-style-type:none;display:inline;'>
								<li><a href='$link'>$course_title</a></li>
							</ul>";
					echo "</div>";
				}//result			
				else{
					echo "";
				}			
			}//end while
			echo "</div>";
		}//if
		#we don't have trending courses yet
		else{
			echo "";
		}
}//class
}
?>
</body>
</html>