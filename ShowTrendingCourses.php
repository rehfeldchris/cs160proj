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
					border-bottom-color:#000000;
					list-style: none outside none;
					font-size:14px;
					min-height: 30px;
					overflow: hidden;
					padding: 10px 10px 10px 15px;
					position: relative;
					width: 97%;
					z-index: 3;
					}
			.trend{
					background: none repeat scroll 0 0 #CCCCCC;
					border: 3px solid #73C0E6;
					border-radius: 10px 10px 10px 30px;
					border-bottom-color:#000000;
					list-style: none outside none;
					font-size:16px;
					font-weight:bold;
					min-height: 30px;
					overflow: hidden;
					padding: 10px 10px 10px 15px;
					position: relative;
					width: 97%;
					z-index: 3;
					}
					
			a{
					color:#000000;
					font-family:Verdana, Arial, Helvetica, sans-serif;
					font-size:18px;
					font-weight:bold;
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
	$query = "SELECT `id` from `trendingcourses` WHERE `hits` >= 5 ORDER BY `hits` ASC LIMIT 5;";
	//run query
	$maxHits = $dbc->query($query) or die($dbc->error);
		
		if($maxHits){
		
			echo "<div id='holder'>";
			//print trending course, with image
					echo "<div class ='trend'>";
						echo"<ul style='list-style-type:none;display:inline;'>
								<li><a href='#'>Trending Courses</a></li>
							</ul>";
					echo "</div>";
					
			while($row = mysqli_fetch_array($maxHits)){
			
				//need id first to find course information
				$id = $row['id'];
				
				//print trending course, with image
				echo "<div class ='course'>";
				echo"<ul style='list-style-type:none;display:inline;'>
						<li><a href='$link'>$course_title</a>
							<div class='image'><img src ='$image[0]' width ='80', height='70'/></div>
						</li>
					</ul>";
				echo "</div>";
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
					
					#just incase we need to show images
					$image_query = $dbc->real_escape_string("SELECT `course_image` from `course_data` WHERE `id` = $id;");
					$image_run = $dbc->query($image_query) or die($dbc->error);
					$image = $image_run->fetch_row();
					
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