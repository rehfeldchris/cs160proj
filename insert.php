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

//set crawling time for 5 min, report all errors
set_time_limit(500);   
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

/**
 * insertCourseDetails, adds primary professors to coursedetails database
 * @param $siteurl, array of site links
 **/

function insertCourseDetails($siteurl){

$factory = new ParserFactory();    

//for each page, get all requested information
foreach ($siteurl as $url) {
    
	try {
		$p = $factory->create($url);  
	} catch (Exception $e) {
		continue;
	}
	
    $p->parse(); 
    if (!$p->isValid()){
		
    }
	
	//to store primary professors	
	$prim_prof = array(); 	
	
	$id;
	$prim_prof = $p->getProfessors();
	$title = mysql_real_escape_string($p->getCourseName());	//$dbc->mysqli_real_escape_string does not work		  
	$short_desc = mysql_real_escape_string($p->getShortCourseDescription());  
	$long_desc =mysql_real_escape_string($p->getLongCourseDescription());				  
	$course_link =mysql_real_escape_string($p->getHomepageUrl());			  
	$video_link ="NA";				 
	//$start_date = $p->getStartDate();						 
	$course_length = $p->getDuration();	     
	$course_image = mysql_real_escape_string($p->getCoursePhotoUrl());						 
	$getcategories =$p->getCategoryNames();	
	$course_date = $p->getStartDate();
	if ($course_date) {
		$course_date = $course_date->format('Y-m-d 00:00:00');
	} /*else {
		$course_date = "Date to be announced"; // this is not necessary
	}*/
	$site = $p->getUniversityName();	
	$category = "";
	
		//collect all gategoires, and build string
		for($start =0; $start != count($getcategories); $start++){
				//if only one element, don't add ','
				if(count($getcategories) == 1 ){
					$category.=$getcategories[$start];
				}
				//if last element, don't add ','
				else if($start+1 == count($getcategories)){
					$category.=$getcategories[$start];
				}
				//need to separate by ','
				else{
					$category.=$getcategories[$start]." ,";
				}
		}
	
		//insert to course_data first STILL NEED TO ADD RETURNED DATEO BJECT..
		$que ="INSERT INTO `sjsucsor_160s1g1`.`course_data` (`id`, `title`, `short_desc`, `long_desc`, `course_link`, `video_link`, 
				`start_date`, `course_length`, `course_image`, 			`category`, `site`)
		 	  VALUES ('0', '$title', '$short_desc', '$long_desc', '$course_link', 'video', '$course_date', '$course_length', '$course_image', '$category', '$site');";
		
		$dbc = $GLOBALS['dbc'];
		//run query
		$dbc->query($que) or die(mysqli_error($dbc));
		//get the last auto generated id, needed for insert to the next table
		$id =mysqli_insert_id($dbc);  
		
		
		//loop through 2d array, and get all the professors who are teaching the class
		foreach($prim_prof as $row){ 
			
			//get the profesor's name and image 
			$name = mysql_real_escape_string($row['name']); 
			$image= mysql_real_escape_string($row['image']); 
			
			//prepare query for inserting to coursedetails table	  											 
		    $sql = "INSERT INTO `sjsucsor_160s1g1`.`coursedetails`(`id`, `profname`, `profimage`)
					VALUES 
					( '$id', '$name', '$image');" or die(mysqli_error());	  
			
			//run query		                   
			$dbc->query($sql) or die(mysqli_error());
		}//end foreach
	}//end outter foreach
}//end function

//clean tables before every crawl
cleanTables();
//insert to coursedetails
insertCourseDetails($courseraUrls);
//insert to course_data 
insertCourseDetails($edxUrls);
exit;
?>
