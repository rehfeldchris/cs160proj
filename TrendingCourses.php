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
    
    $hostInfo = parse_url($url);

	//make sure the url was able to be parsed, which validates that its probably not some junk value
	if($hostInfo !== false){
		//make sure host name is either Coursera or Edx
        // todo? it might be better to allow urls without www. prefix
		if((strcasecmp($hostInfo["host"],"www.coursera.org") == 0) || ((strcasecmp($hostInfo["host"],"www.edx.org") ==0))){
            //redirect to the url while incrementing hits for this url
            //but only after verifying its an edx or coursera url.
            // we reject other stuff, espescially if parsing failed
            header("Location: $url",true) ;
            
            //todo: manzoor, dont forget to escape all values you use in sql querys

			
			//find id of given url
			$idQuery ="SELECT id FROM `course_data` WHERE course_link = '%{$url}%'";
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
		} else {
            echo "not coursera or edx";
        }
	} else {
        echo "url parsing failed.";
    }
?>
