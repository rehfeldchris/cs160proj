<?php

/**
 *insert.php cleans alltables in the database, craws all URLs of Edx and Coursera,
 *and adds all returned data into the course_data and coursedetails tables.
 *
 * @author Manzoor Ahmed
 * @author Chris Rehfeld
 * @author Tatiana Braginets
 */
header('content-type: text/plain;charset=utf-8');

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

$edxUrls = array();
$courseraUrls = array();

//get all urls from coursera 
$coursera_urls_parser = new CourseraUrlsParser();
$coursera_urls_parser->parse();
$courseraUrls = $coursera_urls_parser->getUrls();

//get all urls from edx
$edx_urls_parser = new EdxUrlsParser();
$edx_urls_parser->parse();
$edxUrls = $edx_urls_parser->getUrls();

//clean tables before every crawl
//cleanTables();
removeOldCourses($edxUrls, "Edx");
removeOldCourses($courseraUrls, "Coursera");

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
	$id;
	$prim_prof = $p->getProfessors();
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
	} 
	
	$site = $dbc->real_escape_string($p->getUniversityName());	
    $category = $dbc->real_escape_string(join(', ', $p->getCategoryNames()));
	
		$find_course_sql = "SELECT id, course_link from course_data 
				WHERE 1 AND course_link='$course_link'";
		$result = $dbc->query($find_course_sql);
		
		if ($result && $result->num_rows) { 
			$row = $result->fetch_array();
			$id = $row['id'];
			
			$update_course_sql = "UPDATE course_data SET title='$title', short_desc='$short_desc', 
					long_desc='$long_desc', video_link='$video_link', 
					start_date='$course_date', course_length='$course_length', course_image='$course_image',
					category='$category', site='$website'
					WHERE 1 AND id='$id'";
			$dbc->query($update_course_sql) or die($dbc->error);
		} 
		else { 
			//insert to course_data first 
			$que ="INSERT INTO `course_data` (`id`, `title`, `short_desc`, `long_desc`, `course_link`, `video_link`, 
					`start_date`, `course_length`, `course_image`, 			`category`, `site`)
				  VALUES ('0', '$title', '$short_desc', '$long_desc', '$course_link', '$video_link', '$course_date', '$course_length', '$course_image', '$category', '$website');";


			//run query
			$dbc->query($que) or die($dbc->error);

			//get the last auto generated id, needed for insert to the next table
			$id =mysqli_insert_id($dbc);  
			
			$hit_query = "INSERT INTO `trendingcourses` (`id` ,`hits`)
						  VALUES ('$id', '0');";

			$dbc->query($hit_query) or die($dbc->error);

			//get all the professors who are teaching the class
			foreach($prim_prof as $row){ 

				//get the profesor's name and image 
				$name = $dbc->real_escape_string($row['name']); 
				$image= $dbc->real_escape_string($row['image']); 

				//prepare query for inserting to coursedetails table	  											 
				$sql = "INSERT INTO `coursedetails` (`id`, `profname`, `profimage`)
						VALUES 
						( '$id', '$name', '$image');" or die($dbc->error);	  
	                   
				$dbc->query($sql) or die($dbc->error);
			}
			// insert into new_courses
			$new_courses_sql = "INSERT INTO new_courses (course_data_id, date_added) VALUES ('$id', now())";
			$dbc->query($new_courses_sql) or die($dbc->error);
		} 
}//end function

/**
 * Removes courses URLs from db that are not in 
 * the passed array
 * 
 * @param array $newUrls new URLs
 * @param string $website which website URLs belong to
 * @return void
 */
function removeOldCourses($newUrls, $website)
{
	$dbc = $GLOBALS['dbc'];
	
	$old_urls_sql = "SELECT id, course_link from course_data 
						WHERE site='$website'";
	
	$result = $dbc->query($old_urls_sql);
	if ($result && $result->num_rows) {
		while ($row = $result->fetch_array()) {
			if (array_search($row['course_link'], $newUrls) === FALSE) {
				$id = $row['id'];
				$delete_sql = "DELETE from course_data WHERE id='$id'";
				$dbc->query($delete_sql) or die($dbc->error);
			}
		}
	}
	return;
}

exit;
?>