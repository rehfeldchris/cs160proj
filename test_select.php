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
			setcookie("id", $class_ids, time()+36000, "/", ".sjsu-cs.org");	//.sjsu-cs.org
		}
	}
	if(isset($_POST['s'])){
		$val = $_POST['s'];
		
		if($val === "Clear"){
			setcookie('id', 0, time()-36000, "/", ".sjsu-cs.org");
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
		echo "<option value = \"none\">-Select a Course-</option>\n";
		
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
		echo "</select>&nbsp&nbsp";
		
		echo "<input type=\"submit\" name=\"s\" value=\"Add\">&nbsp";
		echo "<input type=\"submit\" name=\"s\" value=\"Clear\"><br>";
		
		//echo "$test_string<br>";
		
		echo "<div>";
		echo "<table border=1 width=\"100%\"  style=\"background-color:FFFFFF\">";
		echo "<tr>
			<td colspan=\"2\" width=\"5%\">Course Title</td>
			<td width=\"5%\">Category</td>
			<td width=\"5%\">Starting Date</td>
			<td width=\"5%\">Duration (Weeks)</td>
			<td colspan=\"2\" width=\"5%\">Instructor</td>
			<td width=\"5%\">Source Website</td>
			</tr>";
			
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
	echo "<!doctype html>
<head>
<title>Kazoom - Compare Classes</title>
<link rel=\"stylesheet\" href=\"css/bootstrap.min.css\" type=\"text/css\">
<link rel=\"stylesheet\" href=\"css/style.css\" type=\"text/css\">
<link rel=\"stylesheet\" href=\"css/jquery-ui-1.10.3.custom.min.css\" type=\"text/css\">
<style>
#searchForm {
    width: 600px;
    margin: auto;
}
#container {
    width: 80%;
    margin: auto;
}

.pagerLinks {
    text-align: right;
}

#search
{
    height: 30px;
    width: 300px;
    border: 1px solid #a4c3ca;
    font: normal 13px 'trebuchet MS', arial, helvetica;
    background: #f1f1f1;
    border-radius: 50px 3px 3px 50px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) inset, 0 1px 0 rgba(255, 255, 255, 1);
    padding-top: 5px;
    padding-right: 9px;
    padding-bottom: 5px;
    padding-left: 9px;
}

#submit
{
    background: #6cbb6b;
    background-image: -moz-linear-gradient(#95d788, #6cbb6b);
    background-image: -webkit-gradient(linear,left bottom,left top,color-stop(0, #6cbb6b),color-stop(1, #95d788));

    -moz-border-radius: 1px 20px 20px 1px;
    border-radius: 3px 50px 50px 3px;
    border-width: 1px;
    border-style: solid;
    border-color: #7eba7c #578e57 #447d43;

     -moz-box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
     -webkit-box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
    box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
    height: 40px;
    padding: 0;
    width: 100px;
    cursor: pointer;
    font: bold 14px Arial, Helvetica;
    color: #23441e;
    text-shadow: 0 1px 0 rgba(255,255,255,0.5);
    margin-top: 0;
    margin-right: 0;
    margin-bottom: 0;
    margin-left: 10px;
}

#submit:hover
{		
    background: #95d788;
    background-image: -moz-linear-gradient(#6cbb6b, #95d788);
    background-image: -webkit-gradient(linear,left bottom,left top,color-stop(0, #95d788),color-stop(1, #6cbb6b));
}	

#submit:active
{		
    background: #95d788;
    outline: none;
     -moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5) inset;
     -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5) inset;
     box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5) inset;		
}


.professor-image {
	margin-top: 1em;
	height:100px;
	overflow: hidden;
	width:100px;
    box-shadow: 2px 3px 9px 2px rgba(0, 0, 0, 0.3);
    border-radius: 5px;
}

.professor-image img{
	display:block;
	min-height:100%;
	max-width:100%;
}

.course-image {
    width: 200px;
    height: 100px;
    box-shadow: 2px 3px 9px 2px rgba(0, 0, 0, 0.3);
    border-radius: 5px;
}

.course-summary {
    position:relative;
    float: left;
    margin: 20px;
    width: 500px;
    height: 300px;
    background-color: #efefef;
    box-shadow: 2px 3px 9px 2px rgba(0, 0, 0, 0.3);
    border-radius: 5px;
}

.hoverable-link:hover {
    background-color: #fff;
    cursor: pointer;
    box-shadow: 2px 3px 9px 2px rgba(0, 215, 255, 0.3);
    transition: all 0.2s ease-out;
}

.course-description {
    font-size: 90%;
}

.course-summary .leftCol, .course-summary .rightCol  {
    padding: 10px;
}
.course-summary .rightCol {
    width: 300px;
    position: absolute;
    top: 0px;
    left:200px;
}

.course-summary .leftCol {
    width: 200px;
}
.course-summary .rightCol {
    width: 240px;
    position: absolute;
    top: 0px;
    left:220px;
}

.course-title {
    margin-top: 1em;
    text-align: center;
}

.course-time {
    margin-top: 1em;
    text-align: center;
	font-size: 90%;
}

</style>
</head>
<body>
	<div id=\"container\">
		<div class=\"row-fluid\">
            <div class=\"pull-right\"><a id=\"subscribe-link\" href=\"subscribe.php\">Subscribe to notifications</a></div>
			<div class=\"pull-right\"><a href=\"d3/index.php\">D3 Visualization</a>&nbsp&nbsp&nbsp&nbsp</div>
            <h3><a class=\"muted\" href=\"index.php\">KaZoom</a></h3>
        </div>";
	printSelect($class_ids);
	echo "</div>
	</body>
	</html>";
?>


