<?

/**
 *@author Manzoor Ahmed
 */

include 'connection.php';
require_once 'AbstractCourseParser.php';
require_once 'ParserFactory.php';

$courseraUrls = array(
      'https://www.coursera.org/course/innovacion'
    , 'https://www.coursera.org/course/interactivepython'
    , 'https://www.coursera.org/course/GTG'
    , 'https://www.coursera.org/course/einstein'
    , 'https://www.coursera.org/course/steinmicro'
    , 'https://www.coursera.org/course/analyticalchem'
    , 'https://www.coursera.org/course/design'
    , 'https://www.coursera.org/course/pgm'
 );

$edxUrls = array(
      'https://www.edx.org/courses/UTAustinX/UT.2.01x/2013_Sept/about'
    , 'https://www.edx.org/courses/HarvardX/SPU27x/2013_Oct/about'
    , 'https://www.edx.org/courses/UTAustinX/UT.1.01x/2013_Sept/about'
    , 'https://www.edx.org/courses/UTAustinX/UT.3.01x/2013_Sept/about'
    , 'https://www.edx.org/courses/UTAustinX/UT.4.01x/2013_Sept/about'
    , 'https://www.edx.org/courses/BerkeleyX/Stat2.2x/2013_April/about'
);

/**
 * insertCourseDetails, adds primary professors to coursedetails database
 * 
 **/

function insertCourseDetails($sitename){


$factory = new ParserFactory();    

foreach ($sitename as $url) {
    $p = $factory->create($url);  
    $p->parse();  			//parse url
    if (!$p->isValid()){
		//echo "$url\*************************";
        //var_dump($p);
    }
	
	$prof = array();    									//to store professors
	$prim_prof = array(); 									//to store primary professors	
	$dumped= array();   									//store dumped array
	
	$name;              									//stores professor's name, which is extracted from sub array 
	$image;													//stores professors's image, .... 
		
	$prof = $p->getPrimaryProfessor();  					//get professors
	$prim_prof = $p->getProfessors();   					//get primary professors
		
		foreach($prim_prof as $row){    					//loop through 2d array
			 
			$name = mysql_real_escape_string($row["name"]); //get the first element
			$image= mysql_real_escape_string($row["image"]);//get the second element
				  											//prepare sql	
		    $sql = "INSERT INTO `sjsucsor_160s1g1`.`coursedetails`(`id`, `profname`, `profimage`)
					VALUES 
					( '', '$name', '$image');" or die(mysql_error());	  
			
			$dbc=$GLOBALS['dbc'];		                   //can't access $dbc whithout grabbing it from GLOBALS['dbc']
			$dbc->query($sql) or die(mysqli_error());	   //insert 
		}//end foreach
	}//end outter foreach
}//end function


function insertCourseData($sitename){



}

insertCourseDetails($courseraUrls);
insertCourseDetails($edxUrls);
exit;
?>