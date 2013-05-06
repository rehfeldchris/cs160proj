<?php

require_once 'connection.php';

ignore_user_abort(true);

if (!isset($_POST['message'], $_POST['courseId'])) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    exit;
}

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    header("Location: courseDetail.php?courseId={$_POST['courseId']}");
}



if (!strlen($_POST['message']) || !ctype_digit($_POST['courseId'])) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    exit;
}

if (get_magic_quotes_gpc()) {
	$_POST['message'] = stripslashes($_POST['message']);
}

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

