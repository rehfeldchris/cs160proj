
<?php
require_once "connection.php";
	
	$class_ids = "";
	
	if(isset($_POST['1'])){
		$val = $_POST['1'];
		if (isset($_COOKIE['id'])) {
			$class_ids = $val.",".$_COOKIE['id'];
		} else {
			$class_ids = $val;
		}
		if($val != "none"){
			setcookie("id", $class_ids, time()+3600);
		}
	}
	if(isset($_POST['s'])){
		$val = $_POST['s'];
		
		if($val === "Clear"){
			setcookie('count', 0, time()-3600);
			setcookie('id', 0, time()-3600);
			setcookie('class[]', 0, time()-3600);
			$class_ids = "";
		}
	}
	
	//foreach ($_COOKIE as $key => $value){
	//	echo("key: ".$key." - val: ".$value.'<br>');
	//}

	function printSelect($class_ids)
	{
		$db=$GLOBALS['dbc'];
		$que="Select distinct * from course_data natural join coursedetails";
		$result=$db->query($que) or die(mysqli_error($db));
		$finfo = $result->fetch_fields();
		
		$i = 0;
		
		echo "<form action=\"test_select.php\" method=\"post\">";
		
		echo "<select name = \"1\">";
		echo "<option value = \"none\">--</option>\n";
		
		$classes = explode(",", $class_ids);
		
		while($row = mysqli_fetch_array($result))
		{
			$id=$row['id'];
			
			//if($id == $_GET['1']){
			//if(in_array($id, $vals)){
			if(in_array($id, $classes)){ 
				$rows[] = $row;
			}

			$title=$row['title'];
			$teacher=$row['profname'];
			echo "<option value = \"$id\">$title with $teacher</option>\n";
			
			$i++;
		}
		echo "</select><br>";
		
		echo "<input type=\"submit\" name=\"s\" value=\"Add\"><br>";
		echo "<input type=\"submit\" name=\"s\" value=\"Clear\"><br>";
		
		//echo "$test_string<br>";
		
		echo "<div>";
		echo "<table border=1 width=\"100%\"  style=\"background-color:FFFFFF\">";
		
		if(!empty($rows)){
			foreach ($rows as $row){
				printRow($row);
			}
		}
		
		echo "</table>";
		
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
	
		//foreach ($row as $elem){
		//	echo "$elem<br>";
		//}
	
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
		
	printSelect($class_ids);
?>

