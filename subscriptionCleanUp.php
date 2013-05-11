<?php

/**
 * Cleans up old courses (> 7 days) and unverified emails (> 3 days)
 * Should be run each day
 * 
 * @author Tatiana Braginets
 * Random string f-n: http://stackoverflow.com/questions/853813/how-to-create-a-random-string-using-php
 */

header('content-type: text/html;charset=utf-8');
require_once 'connection.php';

// ====================================== DAILY =============================
deleteEmails();

// ====================================== WEEKLY =============================
$todays_date = date("D");
if ($todays_date === "Sun") {
	deleteCourses();
}

function deleteEmails()
{
	$dbc = $GLOBALS['dbc'];
	$que = "DELETE FROM subscription_emails 
			WHERE 1 AND verified='0' AND date_added<DATE(now()) - interval 3 day";
	$dbc->query($que) or die($dbc->error);
	echo "deleted emails: " . $dbc->affected_rows . "\n";
}

function deleteCourses()
{
	$dbc = $GLOBALS['dbc'];
	$que = "DELETE FROM new_courses 
			WHERE 1 AND date_added<DATE(now()) - interval 7 day";
	$dbc->query($que) or die($dbc->error);
	echo "deleted courses: " . $dbc->affected_rows . "\n";
}

?>