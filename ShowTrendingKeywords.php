<?php 
/**
 * Script used for seraching and outputting keywords
 * @Author Manzoor Ahmed
 **/
require_once 'connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_POST['submit']))
{
	$dbc =$GLOBALS['dbc'];
	$word = $_POST['search'];
	
		//did not find it, just add and increment hits
		//look in `course_data` table for course name and link
		$course = "SELECT * FROM `keywords` WHERE `title` LIKE '$word%' OR `short_desc` LIKE '$word%' OR `link` LIKE '$word%';" or die($dbc->error);
		$run_course = $dbc->query($course) or die ($dbc->error);
		//$total_rows = mysqli_fetch_array($run_course);
		
		if(mysqli_num_rows($run_course) != 0){
		
			while($row = mysqli_fetch_array($run_course)){
				
				$id = $row[0];	
				$title = $row[1];
				$link = $row[2];	
				$short_desc = $row[3];
				$old_hit = $row[4];	
				
				echo "<a href='$link' style='color:green;'>$title</a>";
				echo "<p>$short_desc</p>";
				echo "</b>";	
				
				$new_hit = $old_hit +1;
				$inc = "UPDATE `keywords` SET `hits` = '$new_hit' WHERE `id` = '$id'";
				$run = $dbc->query($inc) or die($dbc->error);	
						
				}//while
			}//if
				
			else if(mysqli_num_rows($run_course) == 0) {
						$in_course = "SELECT * from `course_data` WHERE  `title` LIKE '$word%' OR `course_link` LIKE '$word%' OR `short_desc` LIKE '$word%';" or die($dbc->error);
						$run_in = $dbc->query($in_course) or die ($dbc->error);
						
						while($row1 = mysqli_fetch_array($run_in)){
			
								$title1 = $row1['title'];
								$short_desc1 = $row1['short_desc'];
								$link1 = $row1['course_link'];	
						
								$insert_1 = "INSERT INTO `keywords` (`id`,`title`,`link`, `short_desc`,`hits`) VALUES ('','$title1','$link1','$short_desc1','');";
								$run_1 = $dbc->query($insert_1) or die($dbc->error);
								
								echo "<a href='$link1' style='color:green;'>$title1</a>";
								echo "<p>$short_desc1</p>";
								echo "</b>";	
								
						}//while
				  }//else if 
				  else{
				  			echo "NO RESULTS FOR " .$word;
							echo "</b>";
				  	  }
}//isset
?>