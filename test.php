<?php
/**
 * File used for testing the parsers.
 * @author Chris Rehfeld
 */


header('content-type: text/plain;charset=utf-8');

require_once 'ParserFactory.php';



$factory = new ParserFactory();

$courseraUrls = array(
      'https://www.coursera.org/course/innovacion'
    , 'https://www.coursera.org/course/interactivepython'
    , 'https://www.coursera.org/course/GTG'
    , 'https://www.coursera.org/course/einstein'
    , 'https://www.coursera.org/course/steinmicro'
    , 'https://www.coursera.org/course/analyticalchem'
    , 'https://www.coursera.org/course/design'
    , 'https://www.coursera.org/course/pgm'
 );

$edxUrls = array(
      'https://www.edx.org/courses/UTAustinX/UT.2.01x/2013_Sept/about'
    , 'https://www.edx.org/courses/HarvardX/SPU27x/2013_Oct/about'
    , 'https://www.edx.org/courses/UTAustinX/UT.1.01x/2013_Sept/about'
    , 'https://www.edx.org/courses/UTAustinX/UT.3.01x/2013_Sept/about'
    , 'https://www.edx.org/courses/UTAustinX/UT.4.01x/2013_Sept/about'
    , 'https://www.edx.org/courses/BerkeleyX/Stat2.2x/2013_April/about'
);


foreach ($courseraUrls as $url) {
    $p = $factory->create($url);
    $p->parse();
    if (!$p->isValid()) {
        echo "$url\n\n\n\n\n";
        var_dump($p);
        exit;
    }
}
foreach ($edxUrls as $url) {
    $p = $factory->create($url, array('shortCourseDescription' => 'foo'));
    $p->parse();
    if (!$p->isValid()) {
        echo "$url\n\n\n\n\n";
        var_dump($p);
        exit;
    }
}



exit;


$edxParser = $factory->create('http://www.edx.org/courses/MITx/14.73x/2013_Spring/about');
$edxParser->parse();
var_dump($edxParser->isValid());
var_dump($edxParser);

echo "-----------------\n";

$courseraParser = $factory->create('https://www.coursera.org/course/interactivepython');
$courseraParser->parse();
var_dump($courseraParser->isValid());
var_dump($courseraParser);