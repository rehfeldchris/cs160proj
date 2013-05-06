<?php

/**
 * Deletes user email from database
 * 
 * @author Tatiana Braginets
 * Random string f-n: http://stackoverflow.com/questions/853813/how-to-create-a-random-string-using-php
 */

header('content-type: text/html;charset=utf-8');
require_once 'connection.php';

?>
<!DOCTYPE html>
<head>
<title>Kazoom - Email subscription</title>
<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
 <div class="container">
	<h3>Unsubscribe from new courses notification</h3>
	<hr class="gradientHr">

<?php

if (isset($_REQUEST['email']) && isset($_REQUEST['key'])) {
	$dbc = $GLOBALS['dbc'];
	$email = $dbc->real_escape_string($_REQUEST['email']);
	$key = $dbc->real_escape_string($_REQUEST['key']);
	
	$que = "DELETE FROM subscription_emails 
			WHERE 1 AND email='$email' AND rand_key='$key'";

	$result = $dbc->query($que);

	if($result && $result->num_rows) {
		echo "You have been sucsessfully unsubscribed.";
	} else {
		echo "The link is not valid. Please, check the link or contact site administrator.";
	}
}
?>
</div>
</body>
</html>