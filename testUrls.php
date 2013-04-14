<?php

require_once 'EdxUrlsParser.php';
require_once 'CourseraUrlsParser.php';


$coursera_urls_parser = new CourseraUrlsParser();
$coursera_urls_parser->parse();
$courseraUrls = $coursera_urls_parser->getUrls();

foreach ($courseraUrls as $url) {
	echo "$url \n";
}

echo "\n\n";

$edx_urls_parser = new EdxUrlsParser();
$edx_urls_parser->parse();
$edxUrls = $edx_urls_parser->getUrls();

foreach ($edxUrls as $url) {
	echo "$url \n";
}

?>
