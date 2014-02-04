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
		  <h4 class="subscribe-holder-heading">Unsubscribe from new courses notifications</h4>
	<hr class="gradientHr">

<?php

if (isset($_REQUEST['email']) && isset($_REQUEST['key'])) {
	$dbc = $GLOBALS['dbc'];
	$email = $dbc->real_escape_string($_REQUEST['email']);
	$key = $dbc->real_escape_string($_REQUEST['key']);
	
	$que = "DELETE FROM subscription_emails 
			WHERE 1 AND email='$email' AND rand_key='$key'";

	$result = $dbc->query($que);

	if($result && $dbc->affected_rows) {
		echo "You have been sucsessfully unsubscribed.";
	} else {
		echo "The link is not valid. Please, check the link or contact site administrator.";
	}
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