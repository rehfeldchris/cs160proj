<?php

/**
 * @author Chris Rehfeld
 */

header('content-type: application/json;charset=utf-8');
//header('content-type: text/plain;charset=utf-8');

require_once 'connection.php';


$sql = "
select site, category, title, course_link from course_data 
";

$result = $dbc->query($sql);
$rows = array();
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

$groups = array();
foreach ($rows as $row) {
	extract($row);
	$letter = mb_strtolower(mb_substr($title, 0, 1, 'utf-8'), 'utf-8');
    $groups[$letter][$site][$category][] = $row;
}




function toD3Structure($arr) {
    //check if it has numeric keys
    //non-numeric means titles and names etc...
    //assume first key represents all the keys types
    if (is_numeric(key($arr))) {
        $ret = array();
        foreach ($arr as $subArr) {
            //$ret[] = array('name' => $subArr['title']);
            $ret[] = $subArr + array('name' => $subArr['title'], 'size' => 2);
        }
        return $ret;
    }
    
    $ret = array();
    foreach ($arr as $key => $subArr) {
        $ret[] = array('name' => $key, 'children' => toD3Structure($subArr));
    }
    return $ret;
}

$dataGroupedByLetters = array_map('toD3Structure', $groups);
//print_r($dataGroupedByLetters);
echo json_encode($dataGroupedByLetters);
?>