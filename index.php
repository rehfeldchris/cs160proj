<html>
<head><title>KaZOOM</title>
<meta name="Author" content="Manzoor Ahmed">
<!--My version of the output--> // 

</head>
<body>


<?php

// your table missing the long description that's why it looks so nice
require_once('connection.php');

$dbc = $GLOBALS['dbc'];
//just noticed, this is wrong query....
$query1 = "SELECT * FROM `sjsucsor_160s1g1`.`coursedetails` first_t, 
						 `sjsucsor_160s1g1`. `course_data` second_t
						  WHERE first_t.id = second_t.id
						 ;" or die(mysqli_error());
	
$data1 = $dbc->query($query1);

echo '<table width="100%" border="0" style="background-color:#D8E7F0">';
	while($row = mysqli_fetch_array($data1)){
	
		echo '<tr>
				<td width="9%"><a href="'.$row['course_link'].'"><img src ="'.$row['course_image'].'" width = "200px;" height="100px;"/></a></td>
				<td width="9%">'.$row['title'].'</td>
				<td width ="20%">&nbsp;</td>
				<td width="24%">'.$row['category'].'</td>
				<td width="8%">'.$row['start_date']. '</td>
				<td width="9%">'.$row['course_length']. ' (days)</td>
				<td width="16%">'.$row['profname']. '</td>
				<td width="13%"><img src = "'.$row['profimage'].'" width="100px;" height="100px"/></td>
				<td width="6%">'.$row['site']. '</td>
			    </tr>';
	}
echo '</table>';

require_once("TableInfo.php");

?>
</body>
</html>
