<?php

header('content-type: text/plain;charset=utf-8');

require_once 'ParserFactory.php';

$factory = new ParserFactory();

$edxParser = $factory->create('http://www.edx.org/courses/MITx/14.73x/2013_Spring/about');
$courseraParser = $factory->create('https://www.coursera.org/course/interactivepython');
$courseraParser->parse();
var_dump($courseraParser);