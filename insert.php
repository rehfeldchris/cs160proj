<?

/**
 *insert.php craws all URLs of Edx and Coursera, and adds all returned data
 *into the course_data and coursedetails tables.
 *@author Manzoor Ahmed
 */

require_once 'connection.php';
require_once 'AbstractCourseParser.php';
require_once 'ParserFactory.php';
require_once 'emptyTables.php';
require_once 'CourseraUrlsParser.php';
require_once 'EdxUrlsParser.php';

set_time_limit(500);   //set crawling time for 5 min

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
 * @param $sitename, array of site links
 **/

function insertCourseDetails($sitename){

$factory = new ParserFactory();    

//for each page, get all requested information
foreach ($sitename as $url) {
    
	$p = $factory->create($url);  
    $p->parse(); 
    if (!$p->isValid()){
		
    }
	
	$prof = array();    	//to store professors
	$prim_prof = array(); 	//to store primary professors	
	
	//course_data variables
	$id;
	$title = $p->getCourseName();			  //course title
	$short_desc =$p->getCourseDescription();  //course short description
	$long_desc ="un-known";					  //course long description
	$course_link ="http://...";				  //course link
	$video_link ="https://...";				  //course video link
	$start_date ="";						  //course start date		
	$course_length = $p->getDuration();	      //course length in week(s)
											
	$course_image = "";						  //course main image
	$category ="";							  //course category
	$site = $p->getUniversityName();		  //course site, ie Edx, Coursera
	
	//coursedetails variables
	$name;              					 //stores professor's name, which is extracted from sub array 
	$image;									 //stores professors's image, .... 
		
	$prof = $p->getPrimaryProfessor();  	//get professors
	$prim_prof = $p->getProfessors();   	//get primary professors
		
		//insert to course_data first: for now just dummy variables!
		$que ="INSERT INTO `sjsucsor_160s1g1`.`course_data` (`id`, `title`, `short_desc`, `long_desc`, `course_link`, `video_link`, 
				`start_date`, `course_length`, `course_image`, 			`category`, `site`)
		 	  VALUES ('0', 'title', 'short', 'long', 'course', 'video', '2013-04-04', '12', 'image', 'charr', 'site');";
		
		$dbc = $GLOBALS['dbc'];
		$dbc->query($que) or die(mysqli_error($dbc)); 
		$id =mysqli_insert_id($dbc);                        //get last auto generated id
		
		foreach($prim_prof as $row){    					 //loop through 2d array
			 
			$name = $dbc->real_escape_string($row["name"]);  //get the first element
			$image= $dbc->real_escape_string($row["image"]); //get the second element
				  											 //prepare sql	
		    $sql = "INSERT INTO `sjsucsor_160s1g1`.`coursedetails`(`id`, `profname`, `profimage`)
					VALUES 
					( '$id', '$name', '$image');" or die(mysqli_error());	  
			
			$dbc=$GLOBALS['dbc'];		                   //can't access $dbc whithout grabbing it from GLOBALS['dbc']
			$dbc->query($sql) or die(mysqli_error());	   //insert 
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
