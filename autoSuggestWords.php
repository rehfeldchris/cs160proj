<?php

ob_start("ob_gzhandler");
header('content-type: application/json;charset=utf-8');
header('cache-control: public;max-age=3600');

require_once 'connection.php';
require_once 'helperFunctions.php';


$cacheFilename = 'autoSuggestWordsCache.json';
$cacheLifetimeSeconds = 600;

if (file_exists($cacheFilename) && is_readable($cacheFilename) && time() - filemtime($cacheFilename) > $cacheLifetimeSeconds) {
	readfile($cacheFilename);
	exit;
}


$words = getAutoSuggestWords($dbc);
$json = json_encode($words);
file_put_contents($cacheFilename, $json);
echo $json;