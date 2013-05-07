<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="author" content="Manzoor Ahmed" />
<title>SEARCH KaZOOM</title>

<style type="text/css">

#search, #submit
{
	float: left;
}

#search
{
	padding: 5px 9px;
	height: 30px;
	width: 500px;
	border: 1px solid #a4c3ca;
	font: normal 13px 'trebuchet MS', arial, helvetica;
	background: #f1f1f1;
	
	-moz-border-radius: 50px 3px 3px 50px;
	 border-radius: 50px 3px 3px 50px;
	 -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) inset, 0 1px 0 rgba(255, 255, 255, 1);
	 -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) inset, 0 1px 0 rgba(255, 255, 255, 1);
	 box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) inset, 0 1px 0 rgba(255, 255, 255, 1);            
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
	margin: 0 0 0 10px;
        padding: 0;
	width: 200px;
	cursor: pointer;
	font: bold 14px Arial, Helvetica;
	color: #23441e;
	
	text-shadow: 0 1px 0 rgba(255,255,255,0.5);
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

</style>
</head>
<body>
<?php 

/**
 * @Author Manzoor Ahmed 
 * Through this seach bar user can search the database
 * We first look in `keywords` table, no result, then we search the `course_data` table
 * we grab all the results and inset it into `keywords` for faster access
 * and for each search keyword, we increment the hit
 **/
 
require_once 'connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_POST['submit']))
{
	$dbc =$GLOBALS['dbc'];
	$word = $_POST['search'];
	
	
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
}//isset
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table width="100%">
	<tr>
	  <td width="28%"><input type="text" name="search" id="search" size="35" value="Search" /><input type="submit" name="submit" id="submit" value="SEARCH KaZOOM" /></td>
		<td width="12%"></td>
	 
	</tr>
</table>
</form>

<?php

/**
 *index.php is tha main page of the site, and shows all the output from databases
 **/
require_once('connection.php');
require_once("TableInfo.php");

?>
</body>
</html>
