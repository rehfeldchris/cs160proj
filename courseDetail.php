<?php

/**
 * shows detailed info about a course, plus user comments on it.
 * @author Chris Rehfeld
 */
header('content-type: text/html;charset=utf-8');
require_once 'connection.php';

if (!isset($_GET['courseId']) || !ctype_digit($_GET['courseId'])) {
    header('HTTP/1.0 404 Not Found');
    exit;
}
$courseId = $_GET['courseId'];


//course data
$sql = "
select title
     , long_desc
     , course_link
     , video_link
     , course_image
     , start_date
     , course_length
     , site
  from course_data
 where id = ?
";

$stmt = $dbc->prepare($sql);
if (!$stmt) {
    exit;
}
$stmt->bind_param('i', $courseId);
$stmt->execute();
$stmt->bind_result(
       $title
     , $long_desc
     , $course_link
     , $video_link
     , $course_image
     , $start_date
     , $course_length
     , $site);

if (!$stmt->fetch()) {
    echo '404';
} else {
    $stmt->free_result();
}


//professors
$sql = "
select profname
     , profimage
  from coursedetails
 where id = ?
";

$stmt = $dbc->prepare($sql);
if (!$stmt) {
    exit;
}
$stmt->bind_param('i', $courseId);
$stmt->execute();
$stmt->bind_result(
       $profname
     , $profimage);

$professors = array();
while ($stmt->fetch()) {
    $professors[] = compact('profname', 'profimage');
}















function escape($str) {
    return htmlspecialchars($str, ENT_NOQUOTES, 'UTF-8');
}



$sql = "
select comment
     , when_posted
  from comments
 where course_data_id = ?
 order
    by when_posted desc
";
$stmt = $dbc->prepare($sql);
if (!$stmt) {
    exit;
}
$stmt->bind_param('i', $courseId);
$stmt->execute();
$stmt->bind_result($comment, $when_posted);
$comments = array();
while ($stmt->fetch()) {
    $comments[] = compact('comment', 'when_posted');
};
$stmt->free_result();


?><!DOCTYPE html>
<head>
    <title><?php echo escape($title); ?></title>
    <link rel="stylesheet" href="css/comments.css" type="text/css">
<style>
    body {font-family: verdana, arial, sans-serif; background-image: url(images/white_sand.png); background-repeat: repeat;}
    #userReviews {width: 800px; margin: auto; }
    .gradientHr {
    border: 0;
    height: 1px;
    background-image: -webkit-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,0.75), rgba(0,0,0,0)); 
    background-image:    -moz-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,0.75), rgba(0,0,0,0)); 
    background-image:     -ms-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,0.75), rgba(0,0,0,0)); 
    background-image:      -o-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,0.75), rgba(0,0,0,0)); 
}
    #addCommentForm textarea {width: 500px; height: 120px;}
    #addCommentForm p {font-size: 12pt;}
    
    #courseData h1 {
        text-align:center;
    }
    
    #courseData {
        margin: 1em;
        padding: 1em;
    }
    
    #professors ul li {
        display: inline;
        list-style-type: none;
    }
    
    #media {
        max-width: 600px;
        float: right;
        text-align: center;
    }
    
    #media img {
        margin: 2em;
        max-width: 100%;
        
    }
    
    .blingBlingImage {
        -webkit-box-shadow:0 0 20px rgba(0,0,0,0.8);
        -moz-box-shadow:0 0 20px rgba(0,0,0,0.8);
        box-shadow:0 0 20px rgba(0,0,0,0.8);
        border-radius: 30px;
        -moz-border-radius: 5px;
        -khtml-border-radius: 5px;
        -webkit-border-radius: 5px;"
    }
    
</style>
</head>
<body>
    <div id="courseData">
        <h1><?php echo escape($title); ?></h1>

        <div id="media">
            <?php
                if ($course_image) {
                    printf('<a href="TrendingCourses.php?url=%s"><img class="blingBlingImage" src="%s"></a><br>', urlencode($course_link), htmlspecialchars($course_image, ENT_QUOTES, 'UTF-8'));
                }
                
                if ($video_link) {
                    // we need to parse the video id out, but theres diff url formats
                    $parts = parse_url($video_link);
                    if ($parts && false !== stripos($parts['host'], 'youtube') && isset($parts['query'])) {
                        parse_str($parts['query'], $queryVars);
                        if (isset($queryVars['v'])) {
                            $id = $queryVars['v'];
                        } elseif (preg_match('~embed/([^/]{5,55})~', $parts['path'], $matches)) {
                            $id = $matches[1];
                        }
                        if ($id) {
                            printf('<iframe id="ytplayer" width="640" height="390" src="http://www.youtube.com/embed/%s" frameborder="0"></iframe>', urlencode($id));
                        }
                    }
                    
                }
                
            ?>
        </div>
        
        <div id="origin">
            <p>From: <?php echo escape($site); ?></p>
            <p>Link: <?php 
                printf('<a href="TrendingCourses.php?url=%1s">%1s</a>', urlencode($course_link), escape($course_link)); 
            ?></p>
            <p>Start date: <?php
                $dt = new Datetime($start_date);
                if ($dt->format('Y') < 2000) {
                    echo "N/A";
                } else {
                    echo $dt->format('M jS, Y');
                }
            ?></p>
            <p>Duration: <?php
                echo $course_length ? escape($course_length) . ' weeks' : 'N/A';
            ?></p>
        </div>
        
        <div id="professors">
            <h3>Professors</h3>
            <?php if ($professors) { ?>
            <ul>
            <?php foreach ($professors as $entry) { ?>
                <li><p><?php
                    if ($entry['profimage']) {
                        printf('<img class="blingBlingImage" src="%s"><br>', $entry['profimage']);
                    }
                    echo escape($entry['profname']); 
                    ?>
                    </p></li>
            <?php } ?>
            </ul>
            <?php } else { ?>
            <p>No staff.</p>
            <?php } ?>
        
        </div>
        
        
        <div id="longDescription">
            <h3>Description</h3>
            <?php echo $long_desc; ?>
        </div>
    </div>
    
    
    <hr class="gradientHr">
    
    <div id="userReviews">
        <form id="addCommentForm" method="post" action="addComment.php">
            <h5>Add a Review</h5>
            <p>No login or email necessary, just post it!</p>
            <p><textarea name="message"></textarea></p>
            <p><input type="hidden" name="courseId" value="<?php echo $courseId; ?>">
            <input type="submit" value="Submit your review"> <span id="ajaxStatus"></span></p>
        </form>
        
        <hr class="gradientHr">

        <div id="comments">
        <?php foreach ($comments as $entry) { ?>
            <div class='box effect7'>
                <div class='comment'><?php echo escape($entry['comment']); ?></div>
            </div>
        <?php } ?>
        </div>
        <div id="paginationControl"><button>Show more comments</button></div>
    </div>
    
    
    
    
    
    
    
    
    
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
    
    
function makeCommentEntry(msg) {
    $comment = $("<div class='box effect7'><div class='comment'></div></div>");
    $comment.find(".comment").text(msg);
    return $comment;
}
    
    
    
    
$(function(){

    
    $("#addCommentForm").submit(function(evt){
        evt.preventDefault();
        var $form = $(this);
        var $message = $form.find("[name='message']");
        $status = $("#ajaxStatus");
        var msg = $message.val();
        $submitButton = $("#addCommentForm :submit");
        
        if (msg.length > 0) {
            $submitButton.attr("disabled", "disabled");
            $status.text("Submitting comment...").css("opacity", "1");
            $.post("addComment.php", $form.serialize())
            .done(function(){
                $status.animate({opacity: 0});
                $message.val("");
                $comment = makeCommentEntry(msg);
                $comment.hide();
                $("#comments").prepend($comment);
                $comment.slideDown("slow");
            })
            .fail(function(){
                $status.text("Failure!").animate({opacity: 1}).animate({opacity: 0});
            })
            .always(function(){
                $submitButton.removeAttr("disabled");
            });
        }
    });
    
    function hidePaginationButtonIfNotNeeded() {
        if ($("#comments > div:hidden").length == 0) {
            $("#paginationControl").hide();
        }; 
    }
    
    $("#paginationControl").click(function(){
        $("#comments > div:hidden").slice(0, 10).slideDown(function(){
            hidePaginationButtonIfNotNeeded();
        });
    });
    
    $("#comments > div").slice(10).hide();
    hidePaginationButtonIfNotNeeded();
});
</script>
    
    
    
    
    
    
    
    
    
</body>
</html>