<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="author" content="Manzoor Ahmed" />
<title></title>
</head>
<body>

<?php 

/**TrendingKeywords  looks for user typed string in the `keyword` table first, if it not there, then it looks `course_data` to find this string
 * For every find it increments the count in `word_hit` table
 * @Author Manzoor Ahmed
 **/
 
require_once 'connection.php';

if(isset($_POST['submit']))
{
	$dbc =$GLOBALS['dbc'];
	
	$word = $_POST['search'];
	//ignore validation, and $dbc->escape_str for now
	
	$find = "SELECT `id` FROM `keywords` WHERE `keywords`.`word` LIKE '$word';";
	$result = $dbc->query($find) or die($dbc->error);
	
	if($result){
		//found it, just increment hits
		$row_id = $result->fetch_row();
		$id = $row_id[0];
		//get hit
		
		$get_hit = "SELECT `hits` FROM `word_hits` WHERE `id` = '$id'";
		$run_get_hit = $dbc->query($get_hit) or die($dbc->error);
		
		$old_row = $run_get_hit->fetch_row();
		
		$old_hit = $old_row[0];
		$new_hit = $old_hit +1;
		
		$inc = "UPDATE `word_hits` SET `hits` = '$new_hit' WHERE `id` = '$id'";
		$run = $dbc->query($inc) or die($dbc->error);
		
		
		if($id == NULL){
	
		//did not find it
		//scan course table and prof table for this word
		//if result,  add  keywords table for faster access (Cache) it
		// TODO
		
		$insert = "INSERT INTO `keywords` (`id`,`word`) VALUES ('0', '$word');";
		$run = $dbc->query($insert) or die($dbc->error);
		
			if($run){
				$id =mysqli_insert_id($dbc);  
				
				$inc_hits = "INSERT INTO `word_hits` (`id`, `hits`) VALUES('$id','0')";
				$run_hits = $dbc->query($inc_hits) or die($dbc->error);
			}
		}
	
	}//if
}//isset
?>

<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<table width="100%">
	<tr>
	  <td width="28%"><input type="text" name="search" id="search" size="35" value="Search" /></td>
		<td width="12%"></td>
	  <td width="60%"><input type="submit" name="submit" id="submit" value="SEARCH" /></td>
	</tr>
</table>
</form>
</body>
</html>
