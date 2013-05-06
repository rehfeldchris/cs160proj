<?php

/**
 * Verifies user email
 * 
 * @author Tatiana Braginets
 * Random string f-n: http://stackoverflow.com/questions/853813/how-to-create-a-random-string-using-php
 */

header('content-type: text/html;charset=utf-8');
require_once 'connection.php';

function randString($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
{
    $str = '';
    $count = strlen($charset);
    while ($length--) {
        $str .= $charset[mt_rand(0, $count-1)];
    }
    return $str;
}
?>
<!DOCTYPE html>
<head>
<title>Kazoom - Email subscription</title>
<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
<style>
	body {
		font-family: verdana, arial, sans-serif; 
		background-image: url(images/white_sand.png); 
		background-repeat: repeat;
		margin-top: 20px;
	}

	h1, h2, h3, h4, h5, h6 {
        font-family: Tahoma, Helvetica, Arial, Sans-Serif;
        font-weight: normal;
        color: #504f4f;
        text-shadow: 0px 2px 1px #bbbaba;
    }
	
	.gradientHr {
		border: 0;
		height: 1px;
		background-image: -webkit-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,0.75), rgba(0,0,0,0));
		background-image: -moz-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,0.75), rgba(0,0,0,0));
		background-image: -ms-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,0.75), rgba(0,0,0,0));
		background-image: -o-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,0.75), rgba(0,0,0,0));
	}


</style>
</head>
<body>
 <div class="container">
	<h3>Subscribe to new courses notification</h3>
	<hr class="gradientHr">

<?php

if (isset($_REQUEST['email']) && isset($_REQUEST['key'])) {
	$dbc = $GLOBALS['dbc'];
	$email = $dbc->real_escape_string($_REQUEST['email']);
	$key = $dbc->real_escape_string($_REQUEST['key']);
	
	$que="SELECT email_id, email, verified, rand_key FROM subscription_emails 
		WHERE 1 AND email='$email' AND rand_key='$key'";
	//echo $que;
	$result = $dbc->query($que);

	if($result && $result->num_rows) {
		$row = $result->fetch_array();
		$email_id = $row['email_id'];
		$verified = $row['verified'];
		if (!$verified) {
			$key = randString(50);
			$que="UPDATE subscription_emails SET verified='1', rand_key='$key' 
				WHERE email_id='$email_id'";
			$dbc->query($que);
		}
		echo "Your email has been verified. Thank you.";
	
	} else {
		echo "Sorry, the link has expired.";
	}

}
?>
</div>
</body>
</html>
<?php
?>