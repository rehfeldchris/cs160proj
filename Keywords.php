<?php

/*******************************************************************
 *search for trending keywords and output the results
 * 
 *@Author Manzoor Ahmed
 ******************************************************************/

class OutputKeywords{

var $maxSearch = 5;  //search for only top 5, 
var $maxShow =5;    //show only 5 rows 

	function showKeywords($dbc){
	
	$query = $dbc->real_escape_string("SELECT * from `keywords` WHERE `hits` >= 5 ORDER BY hits DESC LIMIT 5;");	
	$query_run = $dbc->query($query) or die($dbc->error);
	
		if($query_run){
		
			echo '<div class="keyword"><span class="muted">Trending Keywords:</span> ';
					
			while($row = mysqli_fetch_array($query_run)){
					
					echo '<a style="text-decoration:underline;" href="index.php?search='. 
							$row[1] . '&submit=KaZoom+It">' . $row[1] . "</a> ";
			}	
			echo "</div>";
		
		}
	}
	
	/**
	* update hits for each keyword
	* @param $dbc, connection to the database
	& @param $word, the keyword which needs to be incremented
	*/
	function updateKeywords($dbc, $word){
		$keyword = $dbc->real_escape_string($word);
		if ($keyword === "") {
			return;
		}
		$keyword = strtolower($keyword);
		$query = "SELECT * FROM keywords WHERE word='$keyword'";	
		$result = $dbc->query($query) or die($dbc->error);
		//echo $query;
		if($result && $result->num_rows) {
			$row = $result->fetch_array();
			$id = $row['id'];
			$hits = $row['hits'];
			$hits++;
			$que = "UPDATE keywords SET hits='$hits'
				WHERE id='$id'";
			
			$dbc->query($que);
		
		} else {
			$que = "INSERT INTO keywords (word, hits)
				  VALUES ('$keyword', '1')";
			$dbc->query($que);
		}
	}//function
	
}//class
?>
