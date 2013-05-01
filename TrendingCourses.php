<?php
 
/**
 * TrendingCourses.php track of clicks on a course link, and increments the count for that link
 * This script is used to find trending courses
 * @Author Manzoor Ahmed
 **/
	
	require_once 'connection.php';
	//race condition
	ignore_user_abort(true);
	//get url 
	$url = $_GET['url'];
	//redirect to the url while incrementing hits for this url
	header("Location: $url",true) ;
	
	//make sure this is correct url 
	if(parse_url($url) !== false){
		
	//check host
	$hostInfo = parse_url($url);

		//make sure host name is either Coursera or Edx
		if((strcmp($hostInfo["host"],"www.coursera.org") == 0) || ((strcmp($hostInfo["host"],"www.edx.org") ==0))){
			
			//find id of given url
			$idQuery ="SELECT id FROM `course_data` WHERE course_link like '%{$url}%'";
			$row = $dbc->query($idQuery) or die($dbc->error);
			//returned column
			$singleColumn = $row->fetch_row();
			//get id number from column
			$id = $singleColumn[0];
			
			//find hits in course_hits table with given id
			$getHits = "SELECT hits FROM `trendingcourses` WHERE id = '$id'";
			//run query, or report error
			$result = $dbc->query($getHits) or die($dbc->error);
			 
			if($result){
				//get returned column
				$oldRow = $result->fetch_row();
				//get id
				$oldNum = $oldRow[0];
				//update hits 
				$newNum = $oldNum+ 1;
				
				//increment count in course_hits table with given id
				$increment = "UPDATE `trendingcourses` SET `hits` = $newNum  WHERE `trendingcourses`.`id` ='$id';";
				$dbc->query($increment) or die($dbc->error);
			}//if
		}//course if
	}//outter
?>
