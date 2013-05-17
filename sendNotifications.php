<?php

/**
 * Sends out email notifications to verified emails
 * 
 * @author Tatiana Braginets
 * Random string f-n: http://stackoverflow.com/questions/853813/how-to-create-a-random-string-using-php
 */

header('content-type: text/html;charset=utf-8');
require_once 'connection.php';


$sites = getSites();
$subject = 'Kazoom new courses notification';
$headers = 'Reply-To: noreply@box334.bluehost.com' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();

// ====================================== DAILY EMAILS =============================
$courses_links = getLinks('d', $sites);

if (count($courses_links)) {
		notify('d', $subject, $headers, $courses_links);
}


// ====================================== WEEKLY EMAILS =============================
$todays_date = date("D");
if ($todays_date === "Sun") {
	$courses_links = getLinks('w', $sites);
	if (count($courses_links)) {
		notify('w', $subject, $headers, $courses_links);
	}
}

function getLinks($freq, $sites)
{
	$dbc = $GLOBALS['dbc'];
	$where = "";
	if ($freq === 'd') {
		$where = ' AND nc.date_added=DATE(now())';
	} else {
		$where = ' AND nc.date_added>DATE(now()) - interval 7 day';
	}
	$courses_links = array();
	foreach ($sites as $site) {
	
		$que = "select nc.course_data_id, nc.date_added, cd.site, cd.title FROM new_courses as nc
				left join course_data AS cd on cd.id = nc.course_data_id
				where 1 AND cd.site='$site' " . $where;
		//echo $que;
		$result = $dbc->query($que);

		if($result) {
			$courses_links[$site] = "";
			while($row = $result->fetch_array()) {
				$courses_links[$site] .= $row['title'] . "\r\n";
				$courses_links[$site] .= "http://www.sjsu-cs.org/cs160/spring2013/sec1group1/courseDetail.php?courseId=" 
										. $row['course_data_id'] . "\r\n\n";
			}
		}
	}
	return $courses_links;
}

function notify($freq, $subject, $headers, $courses_links) 
{
	$dbc = $GLOBALS['dbc'];
	$que = "SELECT email, verified, rand_key, sites, frequency FROM subscription_emails 
			WHERE 1 AND verified='1' AND frequency='$freq'";
	$result = $dbc->query($que);
	
	if($result && $result->num_rows) {
		while($row = $result->fetch_array()) {
			$message = "";
			$email = $row['email'];
			$key = $row['rand_key'];
			$user_sites = explode(',', $row['sites']);
			foreach($user_sites as $site) {
				if ($courses_links[$site] != "") {
					$message .= $site . ": \r\n\n";
					$message .= $courses_links[$site] . "\r\n";
				}	
			}
			if ($message != "") {
				$message .= "To unsubscribe, click link below: \r\n";
				$message .= 'http://www.sjsu-cs.org/cs160/spring2013/sec1group1/unsubscribe.php?email=' 
							. $email . '&key=' . $key;

				mail($email, $subject, $message, $headers);
				echo '<p>Email sent to ' . $email . "</p>";
				echo '<p>Message: ' . $message . "</p>";
			}
		}
	}
}

function getSites() 
{
	$dbc = $GLOBALS['dbc'];
	$que="SELECT DISTINCT site FROM course_data";
	$result = $dbc->query($que);
	$ret = array();
	if($result) { 
		while($row = $result->fetch_array()){
			$ret[] = $row['site'];
		}
	}
	return $ret;
}

?>