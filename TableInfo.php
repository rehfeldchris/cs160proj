
<?php
/**
 *Tableinfo.php prints all data from database
 *@Author Andrey Andreev
 **/
require_once "connection.php";

	function printTable($word)
	{
		$db=$GLOBALS['dbc'];
		$db->real_escape_string($word);
		$que="Select distinct * from course_data natural join coursedetails
			WHERE  title LIKE '%$word%' OR short_desc LIKE '%$word%' 
			GROUP BY title LIMIT 10";
		$result=$db->query($que) or die(mysqli_error($db));
		$finfo = $result->fetch_fields();
		
		echo "<div>";
		echo "<table border=1 width=\"100%\"  style=\"background-color:00FF66\">";
		echo "<tr>";
		$success = false;
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
			
			$link = 'TrendingCourses.php?url='.urlencode($courseLink);
			
			echo "<tr>
			<td width=\"5%\"><img src=\"$courseImage\" width=\"200px\" height=\"100px\"></td>
			<td width=\"5%\"><a href=\"$link\" id='sitelink'>$title</a></td>
			<td width=\"5%\">$categ</td>
			<td width=\"5%\">$startTime</td>
			<td width=\"5%\">$duration</td>
			<td width=\"5%\">$teacher</td>
			<td width=\"5%\"><img src=\"$teacherImage\" width=\"100px\" height=\"100px\"></td>
			<td width=\"5%\">$site</td>
			</tr>";
			$success = true;
		}		
		echo "</tr>";
		echo "</table>";
		return $success;
	}
	
?>

