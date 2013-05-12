<?php

ob_start("ob_gzhandler");
header('content-type: application/json;charset=utf-8');
header('cache-control: public;max-age=3600');

require_once 'connection.php';
require_once 'helperFunctions.php';



$words = getAutoSuggestWords($dbc);
echo json_encode($words);