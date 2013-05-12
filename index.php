<?php
header('content-type: text/html;charset=utf-8');
require_once 'connection.php';
require_once 'Pager/Pager.php';
require_once 'helperFunctions.php';

$search = '';
$pagerLinks = '';
$searchResults = array();

$search = isset($_GET['search']) && is_string($_GET['search'])
        ? (get_magic_quotes_gpc() ? stripslashes($_GET['search']) : $_GET['search'])
        : '';


// split the string into words based on word boundaries(transitions between word charaters and punctuation/whitespace)
$words = array();
foreach (preg_split('~\W+~', $search) as $word) {
    //filter out short stuff
    if (strlen($word) > 1) {
        $words[] = $word;
    }
}

//if any words are present after filtering/processing
if ($words) {
    
    $rows = getSearchResults($dbc, $words);
    $pager = @Pager::factory(array(
        'mode'       => 'Sliding',
        'perPage'    => 10,
        'delta'      => 2,
        'itemData'   => $rows
    ));
    $searchResults = $pager->getPageData();
	if ($searchResults) {
		foreach ($words as $word) {
			recordKeywordSearch($dbc, $word);
		}
	}
    $pagerLinks = $pager->links;
}


$trendingCourses = getTrendingCourses($dbc, 4);
$trendingKeywords = getTrendingKeywords($dbc, 4);



?>
<!doctype html>
<head>
<title>Kazoom - Search</title>
<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="css/style.css" type="text/css">
<link rel="stylesheet" href="css/jquery-ui-1.10.3.custom.min.css" type="text/css">
<style>
#searchForm {
    width: 600px;
    margin: auto;
}
#container {
    width: 80%;
    margin: auto;
}

.pagerLinks {
    text-align: right;
}

#search
{
    height: 30px;
    width: 300px;
    border: 1px solid #a4c3ca;
    font: normal 13px 'trebuchet MS', arial, helvetica;
    background: #f1f1f1;
    border-radius: 50px 3px 3px 50px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) inset, 0 1px 0 rgba(255, 255, 255, 1);
    padding-top: 5px;
    padding-right: 9px;
    padding-bottom: 5px;
    padding-left: 9px;
}

#submit
{
    background: #6cbb6b;
    background-image: -moz-linear-gradient(#95d788, #6cbb6b);
    background-image: -webkit-gradient(linear,left bottom,left top,color-stop(0, #6cbb6b),color-stop(1, #95d788));

    -moz-border-radius: 1px 20px 20px 1px;
    border-radius: 3px 50px 50px 3px;
    border-width: 1px;
    border-style: solid;
    border-color: #7eba7c #578e57 #447d43;

     -moz-box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
     -webkit-box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
    box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
    height: 40px;
    padding: 0;
    width: 100px;
    cursor: pointer;
    font: bold 14px Arial, Helvetica;
    color: #23441e;
    text-shadow: 0 1px 0 rgba(255,255,255,0.5);
    margin-top: 0;
    margin-right: 0;
    margin-bottom: 0;
    margin-left: 10px;
}

#submit:hover
{		
    background: #95d788;
    background-image: -moz-linear-gradient(#6cbb6b, #95d788);
    background-image: -webkit-gradient(linear,left bottom,left top,color-stop(0, #95d788),color-stop(1, #6cbb6b));
}	

#submit:active
{		
    background: #95d788;
    outline: none;
     -moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5) inset;
     -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5) inset;
     box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5) inset;		
}


.professor-image {
	margin-top: 1em;
	height:100px;
	overflow: hidden;
	width:100px;
    box-shadow: 2px 3px 9px 2px rgba(0, 0, 0, 0.3);
    border-radius: 5px;
}

.professor-image img{
	display:block;
	min-height:100%;
	max-width:100%;
}

.course-image {
    width: 200px;
    height: 100px;
    box-shadow: 2px 3px 9px 2px rgba(0, 0, 0, 0.3);
    border-radius: 5px;
}

.course-summary {
    position:relative;
    float: left;
    margin: 20px;
    width: 500px;
    height: 300px;
    background-color: #efefef;
    box-shadow: 2px 3px 9px 2px rgba(0, 0, 0, 0.3);
    border-radius: 5px;
}

.hoverable-link:hover {
    background-color: #fff;
    cursor: pointer;
    box-shadow: 2px 3px 9px 2px rgba(0, 215, 255, 0.3);
    transition: all 0.2s ease-out;
}

.course-description {
    font-size: 90%;
}

.course-summary .leftCol, .course-summary .rightCol  {
    padding: 10px;
}
.course-summary .rightCol {
    width: 300px;
    position: absolute;
    top: 0px;
    left:200px;
}

.course-summary .leftCol {
    width: 200px;
}
.course-summary .rightCol {
    width: 240px;
    position: absolute;
    top: 0px;
    left:220px;
}

.course-title {
    margin-top: 1em;
    text-align: center;
}

.course-time {
    margin-top: 1em;
    text-align: center;
	font-size: 90%;
}

</style>
</head>
<body>
    <div id="container">
        <div class="row-fluid">
            <a class="pull-right" id="subscribe-link" href="subscribe.php">Subscribe to notifications</a>
            <h3><a class="muted" href="index.php">KaZoom</a></h3>
        </div>

        <div class="container-fluid">
          <div class="row">
            <div class="span6">
                <form id="searchForm" class="form-inline" method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>">
                    <h5>Search for courses</h5>
                    <p><input name="search" id="search" placeholder="Enter keywords" autofocus required value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"> 
                       <input type="submit" id="submit" value="KaZoom It"></p>
                </form>
            </div>
            <div class="span2 pull-right">
                <h5>Trending Keywords</h5>
                <ul>
                    <?php foreach ($trendingKeywords as $word) { ?>
                    <li><?php printf('<a href="?search=%1$s">%1$s</a>', htmlspecialchars($word, ENT_QUOTES, 'UTF-8')); ?></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="span4  pull-right">
                <h5>Trending Courses</h5>
                <ul>
                    <?php foreach ($trendingCourses as $course) { ?>
                    <li><?php printf('<a href="courseDetail.php?courseId=%d">%s</a>', $course['id'], htmlspecialchars($course['title'], ENT_QUOTES, 'UTF-8')); ?></li>
                    <?php } ?>
                </ul>
            </div>
          </div>
        </div>
        
        <?php if ($searchResults) { ?>
        <div id="searchResults">
            <div class="pagerLinks">
                <hr class="gradientHr">
                <?php echo $pagerLinks; ?>
            </div>

            
            <div>

                <?php foreach($searchResults as $course) { ?>
                <div class="course-summary">
                    <div class="leftCol">
                        <div class="course-image">
                            <a class="course-detail-link" href="courseDetail.php?courseId=<?php echo $course['id']; ?>">
                                <img class="course-image" src="<?php echo htmlspecialchars($course['course_image'], ENT_QUOTES, 'UTF-8'); ?>">
                            </a>
                        </div>
                        <div class="course-title">
                            <a href="courseDetail.php?courseId=<?php echo $course['id']; ?>">
                                <?php echo htmlspecialchars($course['title'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </div>
						<div class="course-time">
                            <p>
                                <?php  
								$dt = new Datetime($course['start_date'] );
								if ($dt->format('Y') < 2000) {
									 //echo "Start date: N/A";
								} else {
									echo "Start date: " . $dt->format('M jS, Y');
								} ?>
							</p>
							<p>
                                <?php echo $course['course_length'] != 0 ? $course['course_length'] . " weeks" : ""; ?>
                            </p>
                        </div>
                    </div>
                    <div class="rightCol">
                        <div class="course-description">
                            <?php echo htmlspecialchars(substr(strip_tags($course['short_desc']), 0, 200), ENT_QUOTES, 'UTF-8'); ?>…
                        </div>
                        <div class="course-instructor">
                            <?php
							
                            if ($course['profimage']) {
								?><div class="professor-image"><?php
                                printf('<img src="%s">', htmlspecialchars($course['profimage'], ENT_QUOTES, 'UTF-8'));
								?></div><?php
                            }
                            if ($course['profname']) {
                                printf("<div>%s</div>", htmlspecialchars($course['profname'], ENT_QUOTES, 'UTF-8'));
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
                
                <div style="clear: both;"></div>
            </div>
            


            <div class="pagerLinks">
                <?php echo $pagerLinks; ?>
                <hr class="gradientHr">
            </div>
        </div>
        <?php } ?>

        <footer>
            <p>&copy; San Jose State University</p>
        </footer>
    </div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="js/jquery-ui/js/jquery-ui-1.10.3.custom.min.js"></script>
<script>
$(function(){
    $(".course-summary")
    .addClass("hoverable-link")
    .click(function(){
        document.location = $(".course-detail-link", this).attr("href");
    });
    
    
    
    //from http://jqueryui.com/autocomplete/#multiple
    $.getJSON('autoSuggestWords.php', function(availableTags) {
        availableTags = availableTags.map(function(val){
            return val.toLowerCase();
        });
        function split( val ) {
          return val.split( /\s+/ );
        }
        function extractLast( term ) {
          return split( term ).pop();
        }

        $("#search")
          // don't navigate away from the field on tab when selecting an item
          .bind( "keydown", function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB &&
                $( this ).data( "ui-autocomplete" ).menu.active ) {
              event.preventDefault();
            }
          })
          .autocomplete({
            minLength: 1,
            delay: 0,
            source: function( request, response ) {
              // delegate back to autocomplete, but extract the last term
              var searchTerm = extractLast(request.term).toLowerCase();
              var matches = $.grep(availableTags, function(val){
                  return val.indexOf(searchTerm) == 0;
              }).slice(0, 5);

              response( matches );
            },
            focus: function() {
              // prevent value inserted on focus
              return false;
            },
            select: function( event, ui ) {
              var terms = split( this.value);
              // remove the current input
              terms.pop();
              // add the selected item
              terms.push( ui.item.value );
              // add placeholder to get the comma-and-space at the end
              terms.push( "" );
              this.value = terms.join( " " );
              return false;
            }
          });
    });

});
</script>
</body>
</html>