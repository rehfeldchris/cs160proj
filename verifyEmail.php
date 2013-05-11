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
<style type="text/css">
body {
	padding-top: 10px;
	padding-bottom: 10px;
}
.subscribe-holder {
        max-width: 500px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: lightgrey;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
}

</style>
</head>
<body>
 <div class="container-fluid">
	 <div class="row-fluid">

		<h3 ><a class="muted" href="index.php">KaZoom</a></h3>
      </div>
	  
      <hr />
	  <div class="subscribe-holder">
		  <h4 class="subscribe-holder-heading">Subscribe to new courses notifications</h4>
	<hr />

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
			$que = "UPDATE subscription_emails SET verified='1', rand_key='$key' 
				WHERE email_id='$email_id'";
			$dbc->query($que);
		}
		echo "Your email has been verified. Thank you.";
	
	} else {
		echo "Sorry, the link has expired.";
	}

}
?>
	Your email has been verified. Thank you.
	</div>
	  <footer>
	<hr />
	<p>&copy; San Jose State University</p>
	</footer>
</div>
</body>
</html>
<?php
?>