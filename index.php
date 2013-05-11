<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<meta name="author" content="Manzoor Ahmed" />
<title>SEARCH KaZOOM</title>
<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
<style type="text/css">
body {
	padding-top: 10px;
	padding-bottom: 10px;
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

a {
	color:green;
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
 
require 'connection.php';
require 'ShowTrendingCourses.php';
//require_once 'OutputSearch.php';
require_once 'Keywords.php';

/**
 *index.php is tha main page of the site, and shows all the output from databases
 **/
error_reporting(E_ALL);
ini_set('display_errors', 1);
$word = "";
$keywords = new OutputKeywords();
if(isset($_REQUEST['submit']))
{
	//ignore validating for now...
	
	$word = $_REQUEST['search'];
	
	//$output = new OutputSearch();
	//$output->searchResults($word,$dbc=$GLOBALS['dbc']);
}//isset
?>
	
	<div class="container-fluid">

      <div class="row-fluid">
		<a class="pull-right" id="subscribe-link" href="subscribe.php">Subscribe to notifications</a>
		<h3 class="muted">KaZoom</h3>
      </div>
	  
      <hr />

      <div class="row-fluid">
			<div class="span7">
				<form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
					<input type="text" name="search" id="search" size="35" placeholder="Search" />
					<input type="submit" name="submit" id="submit" value="KaZoom It" />
				</form>
				<div class="row-fluid"><div class="span12">
				<?php $keywords->showKeywords($GLOBALS['dbc']); ?>
					</div></div>
			</div>
		<div class="span5">
			<?php 
				$trend = new ShowTrendingCourses(); 
				$trend->showTrendingCourses($dbc =$GLOBALS['dbc']);
		?>
			</div>
		
      </div>

      <hr />

<?php
//prints output table 
require_once("TableInfo.php");
$success = printTable($word);
if ($success) {
	$keywords->updateKeywords($GLOBALS['dbc'], $word);
} else {
	echo '<p class="text-error">' . "No results" . '</p>';
}

?>

</div>
<footer>
	<hr />
	<p>&copy; San Jose State University</p>
    
</footer>
	  
</body>
</html>
