<?php
/**
 * Combined tests for parsing: get urls and parse course info
 * @author Tatiana Braginets
 */

header('content-type: text/plain;charset=utf-8');

require_once 'ParserFactory.php';
require_once 'EdxUrlsParser.php';
require_once 'CourseraUrlsParser.php';

$factory = new ParserFactory();

$coursera_urls_parser = new CourseraUrlsParser();
$coursera_urls_parser->parse();
$courseraUrls = $coursera_urls_parser->getUrls();

$edx_urls_parser = new EdxUrlsParser();
$edx_urls_parser->parse();
$edxUrls = $edx_urls_parser->getUrls();

$repeat = 0;
foreach ($edxUrls as $url) {
	$extraInfo = array('shortCourseDescription' => $edx_urls_parser->getCourseShortDesc($url));
	$edxParser = $factory->create($url, $extraInfo);
	$edxParser->parse();
	echo "Url: " . $url . "\n";
	if ($edxParser->isValid()) {
		echo "University Name: " . $edxParser->getUniversityName() . "\n";
		echo "Name: " . $edxParser->getCourseName() . "\n";
		echo "Duration: " . $edxParser->getDuration() . "\n";
		echo "Image: " . $edx_urls_parser->getCourseImage($url) . "\n";
		echo "Short Description: " . $edx_urls_parser->getCourseShortDesc($url) . "\n";
	}
	echo "---\n";
	$repeat++;
	if ($repeat == 3) {
		break;
	}
}

echo "-----------------\n";

$repeat = 0;
foreach ($courseraUrls as $url) {
	
	$courseraParser = $factory->create($url);
	$courseraParser->parse();
	echo "Url: " . $url . "\n";
	if ($courseraParser->isValid()) {
		echo "Name: " . $courseraParser->getCourseName() . "\n";
		echo "Duration: " . $courseraParser->getDuration() . "\n";
		echo "Image: " . $coursera_urls_parser->getCourseImage($url) . "\n";
	}
	echo "---\n";
	$repeat++;
	if ($repeat == 3) {
		break;
	}
}