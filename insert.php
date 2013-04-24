<?php

/**
 *insert.php cleans alltables in the database, craws all URLs of Edx and Coursera,
 *and adds all returned data into the course_data and coursedetails tables.
 *
 *@author Manzoor Ahmed
 */

require_once 'connection.php';
require_once 'AbstractCourseParser.php';
require_once 'ParserFactory.php';
require_once 'emptyTables.php';
require_once 'CourseraUrlsParser.php';
require_once 'EdxUrlsParser.php';

//set crawling time for 10 min, report all errors
set_time_limit(1200);   
error_reporting(E_ALL);
ini_set('display_errors', 1);

$edxUrls= array();
$courseraUrls =array();

//get all urls from coursera 
$coursera_urls_parser = new CourseraUrlsParser();
$coursera_urls_parser->parse();
$courseraUrls = $coursera_urls_parser->getUrls();

//get all urls from edx
$edx_urls_parser = new EdxUrlsParser();
$edx_urls_parser->parse();
$edxUrls = $edx_urls_parser->getUrls();

//clean tables before every crawl
cleanTables();
//insert to coursedetails
foreach ($courseraUrls as $url) {
    insertCourseDetails($url);
}
foreach ($edxUrls as $url) {
    $extraInfo = array('shortCourseDescription' => $edx_urls_parser->getCourseShortDesc($url));
    insertCourseDetails($url, $extraInfo,"Edx");
}

/**
 * insertCourseDetails, adds primary professors to coursedetails database
 * @param $url, array of site links
 * @param $extraInfo, extra information [Optional]
 * @param $website, the site name where the $url is being passed by
 **/

function insertCourseDetails($url, $extraInfo = array(),$website="Coursera"){
    //mysqli db connection
    $dbc = $GLOBALS['dbc'];
    
    $factory = new ParserFactory();    

    echo "$url\n";

	try {
		$p = $factory->create($url, $extraInfo);
        	$p->parse();
	} catch (Exception $e) {
        //these really should be logged....but print to stdout for now
        echo "parsing failure for $url\n";
        echo $e->getMessage(), "\n", $e->getTraceAsString();
		return false;
	}

    if (!$p->isValid()){
        echo "invalid parser for $url\n";
	return false;
    }
	
	//to store primary professors	
	$prim_prof = $p->getProfessors();
	$title = $dbc->real_escape_string($p->getCourseName());	
    	$title = $dbc->real_escape_string($p->getCourseName());
	$short_desc = $dbc->real_escape_string($p->getShortCourseDescription());  
	$long_desc = $dbc->real_escape_string($p->getLongCourseDescription());				  
	$course_link = $dbc->real_escape_string($p->getHomepageUrl());			  
	$video_link = $dbc->real_escape_string($p->getVideoUrl());
				 
	$course_length = $p->getDuration();	     
	$course_image = $dbc->real_escape_string($p->getCoursePhotoUrl());						 
	$getcategories =$p->getCategoryNames();	
	$course_date = $p->getStartDate();
	if ($course_date) {
		$course_date = $course_date->format('Y-m-d 00:00:00');
	} /*else {
		$course_date = "Date to be announced"; // this is not necessary
	}*/
	$site = $dbc->real_escape_string($p->getUniversityName());	

        
    $category = $dbc->real_escape_string(join(', ', $p->getCategoryNames()));
	
		//insert to course_data first 
		$que ="INSERT INTO `sjsucsor_160s1g1`.`course_data` (`id`, `title`, `short_desc`, `long_desc`, `course_link`, `video_link`, 
				`start_date`, `course_length`, `course_image`, 			`category`, `site`)
		 	  VALUES ('0', '$title', '$short_desc', '$long_desc', '$course_link', '$video_link', '$course_date', '$course_length', '$course_image', '$category', '$website');";
		
		
		//run query
		$dbc->query($que) or die($dbc->error);
		//get the last auto generated id, needed for insert to the next table
		$id =mysqli_insert_id($dbc);  
		
		
		//loop through 2d array, and get all the professors who are teaching the class
		foreach($prim_prof as $row){ 
			
			//get the profesor's name and image 
			$name = $dbc->real_escape_string($row['name']); 
			$image= $dbc->real_escape_string($row['image']); 
			
			//prepare query for inserting to coursedetails table	  											 
		    $sql = "INSERT INTO `sjsucsor_160s1g1`.`coursedetails`(`id`, `profname`, `profimage`)
					VALUES 
					( '$id', '$name', '$image');" or die($dbc->error);	  
			
			//run query		                   
			$dbc->query($sql) or die($dbc->error);
		}//end foreach

}//end function


exit;
?>
