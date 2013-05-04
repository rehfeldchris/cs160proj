<?php
 
/**
 * TrendingCourses.php increments count for a clicked link in trendingcourses table
 * This script is used to count hits for each course
 * @Author Manzoor Ahmed
 **/
	
	require_once 'connection.php';
	require_once 'ParserFactory.php';
	
	#race condition
	ignore_user_abort(true);
	
	#get url 
	$url = $_GET['url'];
    $hostInfo = parse_url($url);
	
	#make sure the url to be parsed, which validates that its probably not some junk value
	if($hostInfo !== false){
		//var_dump($hostInfo);
		
		#make sure host name is either Coursera or Edx
		if((strcasecmp($hostInfo["host"],"www.coursera.org") == 0) || ((strcasecmp($hostInfo["host"],"www.edx.org") ==0))){
            
			#check for '/course/'
			$hostpath = $hostInfo['path'];
			#'course' is subpath for coursera , and 'courses' is subpath for edx
			$coursepath = explode('/',$hostpath);
			
			#this is a coursera or edx url, ie www.coursera.org/course/.. or www.edx.org/courses/... 
			if( ((strcasecmp($hostInfo["host"],"www.coursera.org")==0) && (strcasecmp($coursepath[1],"course")==0))
			||  ((strcasecmp($hostInfo["host"],"www.edx.org")==0) && (strcasecmp($coursepath[1],"courses")==0))){
				
			   /**
				* redirect to the url while incrementing hits for this url
            	* but only after verifying it's an edx or coursera url
            	* we reject other stuff, espescially if parsing failed
       			*/
				
				#try to parse ther $url; we might not need this. 
				$factory = new ParserFactory(); 
						
				try {
					$p = $factory->create($url, $extraInfo= array());
        			$p->parse();
				} catch (Exception $e) {
        					
				}

				if (!$p->isValid()){
					#redirect user back to index.php
					header("Location: index.php",true);
				}
				
				#we parsed the url
				else{	
					#redirect user to $url
					header("Location: $url",true) ;
					
					#find id of given url
					$idQuery ="SELECT id FROM `course_data` WHERE `course_link` LIKE '%{$url}%';";
					$row = $dbc->query($idQuery) or die($dbc->error);
					
					#returned column
					$singleColumn = $row->fetch_row();
					#get id number from column
					$id = $singleColumn[0];
					echo $id;
					
					#find hits in course_hits table with given id
					$getHits = "SELECT hits FROM `trendingcourses` WHERE `id` = '$id';";
					#run query, or report error
					$result = $dbc->query($getHits) or die($dbc->error);
						
						if($result){
							#get returned column
							$oldRow = $result->fetch_row();
							#get id
							$oldNum = $oldRow[0];
							#update hits 
							$newNum = $oldNum+ 1;
							
							#increment count in course_hits table with given id
							$increment ="UPDATE `trendingcourses` SET `hits` = $newNum  WHERE `trendingcourses`.`id` ='$id';";
							$dbc->query($increment) or die($dbc->error);
					   }//if	
				}//else		   
		  }//if course
		  
	}//if hostname
	else{
		echo "Not valid Coursera or Edx URL";
	}
 }//hostinfo 
 else{
 	echo "Unknown URL ";
 }	
?>
