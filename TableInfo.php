<?php
require_once "connection.php";

	
	
	
	{
		$db=$GLOBALS['dbc'];
		$que="Select distinct * from course_data natural join coursedetails";
		$result=$db->query($que) or die(mysqli_error($db));
		$finfo = $result->fetch_fields();
		
		echo "<div>";
		echo "<table border=1 width=\"100%\"  style=\"background-color:00FF66\">";
		echo "<tr>";
		while($row = mysqli_fetch_array($result))
		{
			$courseImage=$row['course_image'];
			$title=$row['title'];
			$categ=$row['category'];
			$startTime=$row['start_date'];
			$duration=$row['course_length'];
			$teacher=$row['profname'];
			$teacherImage=$row['profimage'];
			$courseLink=$row['course_link'];
			echo "<tr>
			<td width=\"5%\"><img src=\"$courseImage\" width=\"100px\" height=\"100px\"></td>
			<td width=\"5%\"><a href=\"$courseLink\">$title</td>
			<td width=\"5%\">$categ</td>
			<td width=\"5%\">$startTime</td>
			<td width=\"5%\">$duration</td>
			<td width=\"5%\">$teacher</td>
			<td width=\"5%\"><img src=\"$teacherImage\" width=\"100px\" height=\"100px\"></td>
			</tr>";
			
			
		}
		
		
		echo "</tr>";
		echo "</table>";
	}


	
		printTable();
?>