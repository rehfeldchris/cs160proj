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
	
	$edxParser = $factory->create($url);
	$edxParser->parse();
//	var_dump($edxParser->isValid());
//	var_dump($edxParser);
	echo $url . "\n";
	if ($edxParser->isValid()) {
		echo $edxParser->getCourseName() . "\n";
		echo $edxParser->getCourseDescription() . "\n";
		echo $edxParser->getDuration() . "\n";
	}
	echo "---\n";
	$repeat++;
	if ($repeat == 1) {
		break;
	}
}

echo "-----------------\n";

$repeat = 0;
foreach ($courseraUrls as $url) {
	
	$courseraParser = $factory->create($url);
	$courseraParser->parse();
//	var_dump($courseraParser->isValid());
//	var_dump($courseraParser);
	echo $url . "\n";
	if ($courseraParser->isValid()) {
		echo $courseraParser->getCourseName() . "\n";
		echo $courseraParser->getCourseDescription() . "\n";
		echo $courseraParser->getDuration() . "\n";
	}
	echo "---\n";
	$repeat++;
	if ($repeat == 1) {
		break;
	}
}