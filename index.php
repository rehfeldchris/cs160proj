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
	height: 30px;
	width: 300px;
	border: 1px solid #a4c3ca;
	font: normal 13px 'trebuchet MS', arial, helvetica;
	background: #f1f1f1;
	
	-moz-border-radius: 50px 3px 3px 50px;
	border-radius: 50px 3px 3px 50px;
	 -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) inset, 0 1px 0 rgba(255, 255, 255, 1);
	 -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) inset, 0 1px 0 rgba(255, 255, 255, 1);
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

</style>
</head>
<body>
<?php 

/**
 * @Author Manzoor Ahmed 
 * @Author Andrey Andreev
 * @Author 
 * @Author
 * Through this seach bar user can search the database
 * We first look in `keywords` table, no result, then we search the `course_data` table
 * we grab all the results and inset it into `keywords` for faster access
 * and for each search keyword, we increment the hit
 **/
 
require_once 'connection.php';
require_once("ShowTrendingCourses.php");
require_once("OutputSearch.php");
require_once("OutputKeywords.php");

/**
 *index.php is tha main page of the site, and shows all the output from databases
 **/
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_POST['submit']))
{
	//ignore validating for now...
	
	$word = $_POST['search'];
	$output = new OutputSearch();
	$output->searchResults($word,$dbc=$GLOBALS['dbc']);
}//isset
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table width="100%">
	<tr>
	  <td width="33%"><input type="text" name="search" id="search" size="35" value="Search" /><input type="submit" name="submit" id="submit" value="KaZoom It" /></td>
		<td width="39%"><div style="padding:0px 0px 10px 20px;">
		<?php
			$keywords = new OutputKeywords();
			$keywords->showKeywords($dbc=$GLOBALS['dbc']);			
		?>
		</div></td>
		<td width="28%"><div id ="search-keywords">
		<?php 
		$trend = new ShowTrendingCourses(); 
		$trend->showTrendingCourses($dbc =$GLOBALS['dbc']);
		?>
	  </div></td>
	</tr>
</table>
</form>

<?php
//prints output table 
require_once("TableInfo.php");
?>

</body>
</html>
