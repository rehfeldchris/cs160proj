<?php

/*
 * This file receives the post request to add a new comment. 
 * 
 * If the request wasn't sent with ajax, it redirects the user back to the page it assumed the browser came from.
 * 
 * @author Chris Rehfeld
 */



require_once 'connection.php';

ignore_user_abort(true);

//make sure data sent
if (!isset($_POST['message'], $_POST['courseId'])) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    exit;
}

//check if request was via ajax. if not, redirect them(but this script will still record their comment)
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    header("Location: courseDetail.php?courseId={$_POST['courseId']}");
}


//make sure data sent
if (!strlen($_POST['message']) || !ctype_digit($_POST['courseId'])) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    exit;
}

if (get_magic_quotes_gpc()) {
	$_POST['message'] = stripslashes($_POST['message']);
}

//add the comment
$sql = "insert into comments (course_data_id, comment, when_posted) values (?, ?, now())";
$stmt = $dbc->prepare($sql);
if (!$stmt) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    exit;
}

$stmt->bind_param('is', $_POST['courseId'], $_POST['message']);
if (!$stmt->execute()) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    exit;
}

//send no status header if we reach here, assume "200 ok" will be sent by server default