<?php
/**
 *TableInfo.php outputs all the content from the database
 *@Author Andrey Andreev
 */

require_once "connection.php";

	//prints all data 
	function printTable()
	{
		$db=$GLOBALS['dbc'];
		//combine tables
		$que="Select distinct * from course_data natural join coursedetails";
		//run query		
		$result=$db->query($que) or die(mysqli_error($db));
		$finfo = $result->fetch_fields();
		
		echo "<div>";
		echo "<table border=1 width=\"100%\"  style=\"background-color:00FF66\">";
		echo "<tr>";

		//while there are more rows in the table		
		while($row = mysqli_fetch_array($result))
		{
			$courseImage=$row['course_image'];
			$title=$row['title'];
			$categ=$row['category'];
			$startTime=$row['start_date'];
			if ($startTime === '0000-00-00') $startTime = 'n/a';
			$duration=$row['course_length'];
			if (!$duration) $duration = 'n/a';
			$teacher=$row['profname'];
			$teacherImage=$row['profimage'];
			$courseLink=$row['course_link'];
			$site=$row['site'];
			$videoLink=$row['video_link'];
			
			echo "<tr>
			<td width=\"5%\"><a href=\"$videoLink\"><img src=\"$courseImage\" width=\"200px\" height=\"100px\"></a></td>
			<td width=\"5%\"><a href=\"$courseLink\">$title</td>
			<td width=\"5%\">$categ</td>
			<td width=\"5%\">$startTime</td>
			<td width=\"5%\">$duration</td>
			<td width=\"5%\">$teacher</td>
			<td width=\"5%\"><img src=\"$teacherImage\" width=\"100px\" height=\"100px\"></td>
			<td width=\"5%\">$site</td>
			</tr>";			
		}		
		echo "</tr>";
		echo "</table>";
	}
		printTable();
?>
