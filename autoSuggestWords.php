<?php
ob_start("ob_gzhandler");
header('content-type: application/json;charset=utf-8');
header('cache-control: public;max-age=3600');

require_once 'connection.php';
require_once 'helperFunctions.php';



$words = getAutoSuggestWords($dbc);
// plugin wants format like [{value: 'foo'}, {value: 'bar'}]
$arrayOfObjs = array();
foreach ($words as $word) {
    $arrayOfObjs[] = array('value' => $word);
}
echo json_encode($words);