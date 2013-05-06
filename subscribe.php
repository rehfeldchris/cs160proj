<?php

/**
 * Gets user emails and subscription options
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

function getSites() 
{
	$dbc = $GLOBALS['dbc'];
	$que="SELECT DISTINCT site FROM course_data";
	$result = $dbc->query($que);
	$ret = "";
	if($result) { 
		while($row = $result->fetch_array()){
			$site = "" . $row['site'];
			$ret .= '<label><input name="sites[]" type="checkbox" value="' . $site .  '" checked>' . $site .'</label><br>';
		}
	}
	return $ret;
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

if (isset($_REQUEST['subscribe'])) {
	
	if (isset($_REQUEST['email']) && isset($_REQUEST['sites']) && isset($_REQUEST['freq'])) {
		$dbc = $GLOBALS['dbc'];
	
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
					echo "You have reached maximum number of confirmation emails sent. 
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
						$message = 'Please confirm you subscription to Kazoom updates by following the link: ';
						$message .= $link;
						$message .= '. If you do not recognize this subscription, please discregard this email.';
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
			$message = 'Please confirm you subscription to Kazoom updates by following the link: ';
			$message .= $link;
			$message .= '. If you do not recognize this subscription, please discregard this email.';
			
			mail($email, $subject, $message, $headers);
			echo "Confirmation email has been sent. Please, click confirmation link in it to verify your email address.";
			
		}
	}
	
} else {
?>
	
<form class="form-horizontal" method="post" action="subscribe.php" onsubmit="return validateForm()">
		<div class="control-group">
		 <label class="control-label" for="email">Email</label>
		 
			<div class="controls">
				<input type="text" id="email" name="email">
				<p class="text-warning" id="email_error"></p>
			</div>
		</div>
		
		<div class="control-group">
		 <label class="control-label" for="sites">Site(s)</label>
		 
			<div class="controls">
				<?php echo getSites(); ?>
				<p class="text-warning" id="sites_error"></p>
			</div>
		</div>
		
		<div class="control-group">
		 <label class="control-label" for="freq">Frequency</label>
			<div class="controls">
				<select name="freq">
				<option value="w" selected>Weekly</option>
				<option value="d">Daily</option>
				</select>
				
			</div>
			
		</div>
		<div class="controls">
				<input type="submit" name="subscribe" value="Subscribe">
			</div>
	</form>
	<?php
}
?>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
	function validateForm()
	{
		var email = $('#email').val();
		if(!email || !countChecked()) {
			if(!email) {
				$('#email_error').html("Please, enter your email");
			} else {
				$('#email_error').html("");
			}
			if(!countChecked()) {
				$('#sites_error').html("At least one site must be checked");
			} else {
				$('#sites_error').html("");
			}
			return false;
		}
		$('#email_error').html("");
		$('#sites_error').html("");
		return true;
	}

	var countChecked = function() {
		return $( "input:checked" ).length;

	};
</script>
</body>
</html>
<?php

?>