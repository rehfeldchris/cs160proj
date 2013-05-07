<?php

/*
 *OutputSearch is used for searching in tables, and prints all the result data
 *@ Author Manzoor Ahmed
 *
 **/

class OutputSearch{

	/**
	 *searchResults the only function which prints all returned data
	 *$word, text input to search for
	 *$dbc, connection to the database
	 */
	function searchResults($word,$dbc){
	
		//look in `keywords` table first 
		$course = "SELECT * FROM `keywords` WHERE `title` LIKE '$word%' OR `short_desc` LIKE '$word%' OR `link` LIKE '$word%';" or die($dbc->error);
		$run_course = $dbc->query($course) or die ($dbc->error);
		
		//found something
		if(mysqli_num_rows($run_course) != 0){
		
			while($row = mysqli_fetch_array($run_course)){
				
				$id = $row[0];	
				$title = $row[1];
				$link = $row[2];	
				$short_desc = $row[3];
				$old_hit = $row[4];	
				
				//outout
				echo "<a href='$link' style='color:green;'>$title</a>";
				echo "<p>$short_desc</p>";
				echo "</b>";	
				//increment the count for this keyword			
				$new_hit = $old_hit +1;
					
				$inc = "UPDATE `keywords` SET `hits` = '$new_hit' WHERE `id` = '$id'";
				$run = $dbc->query($inc) or die($dbc->error);	
						
				}//while
			}//if
			
			//did not find it 
			else if(mysqli_num_rows($run_course) == 0) {
						//search in course_data table
						$in_course = "SELECT * from `course_data` WHERE  `title` LIKE '$word%' OR `course_link` LIKE '$word%' OR `short_desc` LIKE '$word%';" or die($dbc->error);
						$run_in = $dbc->query($in_course) or die ($dbc->error);
						
						while($row1 = mysqli_fetch_array($run_in)){
			
								$title1 = $row1['title'];
								$short_desc1 = $row1['short_desc'];
								$link1 = $row1['course_link'];	
						
								//insert to keywords table, for faster access (This is our cache table)
								$insert_1 = "INSERT INTO `keywords` (`id`,`title`,`link`, `short_desc`,`hits`) VALUES ('','$title1','$link1','$short_desc1','');";
								$run_1 = $dbc->query($insert_1) or die($dbc->error);
								
								//output
								echo "<a href='$link1' style='color:green;'>$title1</a>";
								echo "<p>$short_desc1</p>";
								echo "</b>";	
								
						}//while
				  }//else if 
				  else{
				  			echo "NO RESULTS FOR " .$word;
							echo "</b>";
				  	  }
	}//end searchResults
}//end class
?>