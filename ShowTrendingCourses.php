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
					background: none repeat scroll 0 0 #64F479 ;
					border: 1px solid #73C0E6;
					border-radius: 30px 15px 0px 0px;
					border-bottom:solid 2px;
					border-bottom-color:#000000;
					color: #000058;
					list-style: none outside none;
					min-height: 50px;
					overflow: hidden;
					padding: 10px 15px 15px 15px;
					position: relative;
					width: 97%;
					z-index: 3;
					}
					
			.course a{
				   color:#000000;
				   font-family:Verdana, Arial, Helvetica, sans-serif;
				   font-size:18px;
				   font-weight:bold;
				}
			.image{
				width:80px;
				height:70px;
				float:right;
				}
		</style>
	
	</head>
<body>
<?php
/**
 *ShowTrendinCourses.php select courses with hits starting from $minHits, and shows only five of them with higer hits
 *
 *@Author Manzoor Ahmed 
 */
 
include 'connection.php';

//we need at least five hits for a course to consider it as a trending course; we can always change this.  
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
	
		echo "<div id='holder'>";
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
				
				//print trending course, format1
				//echo"<a href ='$link' style='color:#0066CC;'>$course_title</a>\t";
				
				$image_query = $dbc->real_escape_string("SELECT `course_image` from `course_data` WHERE `id` = $id;");
				$image_run = $dbc->query($image_query) or die($dbc->error);
				$image = $image_run->fetch_row();
				
				
				//print trending course, with image
				echo "<div class ='course'>";
					echo"<ul style='list-style-type:none;display:inline;'>
							<li><a href='$link'>$course_title</a>
								<div class='image'><img src ='$image[0]' width ='80', height='70'/></div>
							</li>
						</ul>";
				echo "</div>";
				
			}//result			
			else{
			    echo "";
			}			
		}//end while
		echo "</div>";
	}//if
	//we don't have trending courses yet
	else{
		echo "";
	}
}//function

showTrendingCourses();

?>
</body>
</html>
