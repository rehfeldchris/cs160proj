<?php

/**
 * Gets user emails and subscription options
 * 
 * @author Tatiana Braginets
 * Random string f-n: http://stackoverflow.com/questions/853813/how-to-create-a-random-string-using-php
 * Regex for email: http://www.regular-expressions.info/email.html
 */

header('content-type: text/html;charset=utf-8');
require_once 'connection.php';

$dbc = $GLOBALS['dbc'];

function randString($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
{
    $str = '';
    $count = strlen($charset);
    while ($length--) {
        $str .= $charset[mt_rand(0, $count-1)];
    }
    return $str;
}

function getSites() 
{
	global $dbc;
	$que="SELECT DISTINCT site FROM course_data";
	$result = $dbc->query($que);
	$ret = "";
	if($result) { 
		while($row = $result->fetch_array()){
			$site = "" . $row['site'];
			$checked = "";
			if (isset($_REQUEST['sites']) 
				&& (array_search($site, $_REQUEST['sites']) !== FALSE)) {
				$checked = "checked";
			}
			$ret .= '<label><input name="sites[]" type="checkbox" value="' 
					. $site .  '" '. $checked . '> ' . $site .'</label><br>';
		}
	}
	return $ret;
}

function getFormErrors() {
	$errors = "";
	global $dbc;
	if (!isset($_REQUEST['email'])
		|| !preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}$/", 
			$dbc->real_escape_string($_REQUEST['email']))) {
		$errors .= '<p class="text-warning">Please enter a valid email</p>';
	} 
	if (!isset($_REQUEST['sites'])) {
		$errors .= '<p class="text-warning">At least one site must be checked</p>';
	}

	return $errors;
}

?>
<!DOCTYPE html>
<head>
<title>Kazoom - Email subscription</title>
<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="css/style.css" type="text/css">
<style type="text/css">
body {
	padding-top: 10px;
	padding-bottom: 10px;
}
.subscribe-holder {
        max-width: 500px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
                
        background-color: #efefef;
        box-shadow: 2px 3px 9px 2px rgba(0, 0, 0, 0.3);
}

</style>
</head>
<body>
	
 <div class="container-fluid">
	 <div class="row-fluid">

		<h3 ><a class="muted" href="index.php">KaZoom</a></h3>
      </div>
	  
      <hr class="gradientHr">
	  <div class="subscribe-holder">
		  <h4 class="subscribe-holder-heading">Subscribe to new course notifications</h4>
          <p>We'll send you an email whenever we find new courses.</p>
	<hr class="gradientHr">
<?php
$errors = '';

if (isset($_REQUEST['subscribe']) && ($errors = getFormErrors()) === "") {
	
	$email = $dbc->real_escape_string($_REQUEST['email']);
	//echo $email;
	//$email = 'tbraginets@gmail.com';
	$freq = $dbc->real_escape_string($_REQUEST['freq']);
	$sites = array();
	foreach($_REQUEST['sites'] as $site) {
		$sites[] = $dbc->real_escape_string($site);
	}
	$site_options = implode(',', $sites);
	//echo $site_options;
		
	$subject = 'Confirm you subscription to Kazoom';
	$headers = 'Reply-To: noreply@box334.bluehost.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
		
	$que="SELECT email, verified, rand_key, max FROM subscription_emails WHERE 1 AND email='$email'";
	$result = $dbc->query($que);
	if($result && $result->num_rows) { 
		$row = $result->fetch_array();
		$verified = $row['verified'];
		if ($verified) {
			echo "You are already subscribed to notifications.";
		} else {
			$max = $row['max'];
			if ($max == 3) {
				echo $email + " You have reached maximum number of confirmation emails sent. 
					Please, make sure you are entering a correct email address and check your spam folder,
					 or contact site administrator for assistance.";
			} else {
				$max++;
				$que="UPDATE subscription_emails SET max='$max'
					WHERE email = '$email'";
					$dbc->query($que);
					$key = $row['rand_key'];
					$link = 'http://www.sjsu-cs.org/cs160/spring2013/sec1group1/cs160proj/verifyEmail.php?email=' 
						. $email . '&key=' . $key;
					$message = 'Please confirm you subscription to Kazoom updates by following the link: ' . "\r\n\n";
					$message .= $link;
					$message .= " \r\n\nIf you do not recognize this subscription, please disregard this email.";
					mail($email, $subject, $message, $headers);
					echo "You have already subscribed to notifications, but have not verified your email address.
						A new confirmation email have been sent. Please, click confirmation link in it to 
						verify your email address.";
			}
		}
		
	} else {
		$key = randString(50);
		$que ="INSERT INTO subscription_emails (email, rand_key, verified, frequency, date_added, sites, max)
			  VALUES ('$email', '$key', '0', '$freq', now(), '$site_options', '1');";
		$dbc->query($que) or die($dbc->error);
			
		$link = 'http://www.sjsu-cs.org/cs160/spring2013/sec1group1/cs160proj/verifyEmail.php?email=' 
			. $email . '&key=' . $key;
		$message = 'Please confirm you subscription to Kazoom updates by following the link: ' . "\r\n\n";
		$message .= $link;
		$message .= " \r\n\nIf you do not recognize this subscription, please disregard this email.";
		
		mail($email, $subject, $message, $headers);
		echo "Confirmation email has been sent. Please, click confirmation link in it to verify your email address.";
			
	}
	?>
	</div>
	<footer>
	<hr class="gradientHr">
	<p>&copy; San Jose State University</p>
	</footer>
	</div>
	</body>
	</html>
	<?php exit();
} else if (isset($_REQUEST['subscribe'])) {
	echo $errors;
} ?>
	
<form class="form-horizontal" method="post" action="subscribe.php" onsubmit="return validateForm();">
		<div class="control-group">
		 <label class="control-label" for="email">Email</label>
		 
			<div class="controls">
				<input type="text" id="email" name="email" value="<?php echo @$_REQUEST['email'] ?>">
				<p class="text-error" id="email_error"></p>
			</div>
		</div>
		
		<div class="control-group">
		 <label class="control-label" for="sites">Site(s)</label>
		 
			<div class="controls">
				<?php echo getSites(); ?>
				<p class="text-error" id="sites_error"></p>
			</div>
		</div>
		
		<div class="control-group">
		 <label class="control-label" for="freq">Frequency</label>
			<div class="controls">
				<select name="freq">
				<option id="w" value="w" 
					<?php if(@$_REQUEST['freq'] === "w") echo "selected"; ?>>Weekly</option>
				<option id="d" value="d" 
					<?php if(@$_REQUEST['freq'] === "d") echo "selected"; ?>>Daily</option>
				
				</select>
				
			</div>
			
		</div>
		<div class="controls">
				<input class="btn btn-success" type="submit" name="subscribe" value="Subscribe">
			</div>
	</form>
</div>
	<footer>
	<hr class="gradientHr">
	<p>&copy; San Jose State University</p>
	</footer>
</div>



<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
function validateForm()
{
		var success = true;
		$('#email_error').html("");
		$('#sites_error').html("");
			
		var email = $('#email').val();
		
		var reg = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}$/;
		if (!email || !reg.test(email) ) {
			$('#email_error').html("Please, enter a valid email");
			success = false;
		} 
		if (!countChecked()) {
			$('#sites_error').html("At least one site must be checked");
			success = false;
		}
		
		return success;
}

var countChecked = function() {
	return $("input:checked").length;

};
</script>
</body>
</html>