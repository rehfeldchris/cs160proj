
<?php
require_once "connection.php";

	//$varClass[] = array(0);
	$varClass = -1;
	if($_POST['formSubmit'] == "Add") 
	{
		$varClass = $_POST['formClass'];
	}

	function printSelect()
	{
		$test_string = "";
	
		$db=$GLOBALS['dbc'];
		$que="Select distinct * from course_data natural join coursedetails";
		$result=$db->query($que) or die(mysqli_error($db));
		$finfo = $result->fetch_fields();
		
		$i = 0;
		$rows = array(0);
		
		echo "<form action=\"test_select.php\" method=\"post\">";
		
		echo "<select name = \"formClass\">";
		echo "<option value = \"none\">--</option>\n";
		while($row = mysqli_fetch_array($result))
		{
			$id=$row['id'];
			
			//if(in_array($id, $varClass)){
			if($id === $varClass){
				$rows[] = $row;
				$test_string .= "aaaa";
			}

			$title=$row['title'];
			$teacher=$row['profname'];
			$link=$row['link'];			//added in this line (jeremy)
			echo "<option value = \"$id\">$title with $teacher</option>\n";
			
			$i++;
		}
		echo "</select><br>";
		
		echo "<input type=\"submit\" name=\"formSubmit\" value=\"Add\"><br>";
		
		echo "$test_string<br>";
		
		foreach ($rows as $row){
			//printRow($row);
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
			echo "<tr>
			<td width=\"5%\"><img src=\"$courseImage\" width=\"200px\" height=\"100px\"></td>
			<td width=\"5%\"><a href=\"$link\" id='sitelink'>$title</a></td>							//wheres $link
			<td width=\"5%\">$categ</td>
			<td width=\"5%\">$startTime</td>
			<td width=\"5%\">$duration</td>
			<td width=\"5%\">$teacher</td>
			<td width=\"5%\"><img src=\"$teacherImage\" width=\"100px\" height=\"100px\"></td>
			<td width=\"5%\">$site</td>
			</tr>";
		}
		
		//printf("%s", $rows[$id]['title']);
	}
	
	function printRow($row){
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
	
		foreach ($row as $elem){
			echo "$elem<br>";
		}
	
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
		
	}
		
	printSelect();
?>

