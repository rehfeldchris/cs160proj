<?php

/**
 *@author Chris Rehfeld
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

    $groups[$site][$category][] = $row;
}


    //$firstCharOfCategory = substr($category, 0, 1);



$groupedAndSplitOnLetters = array();
foreach ($groups as $site => $categories) {
    foreach ($categories as $category => $rows) {
        if (count($rows) > 10) {
            if (strlen(trim($category))) {
                foreach ($rows as $row) {
                    $letter = substr($row['title'], 0, 1);
                    $groupedAndSplitOnLetters[$site][$category][$letter][] = $row;
                }
            } else {
                unset($groupedAndSplitOnLetters[$site][$category]);
                foreach ($rows as $row) {
                    $letter = substr($row['title'], 0, 1);
                    $groupedAndSplitOnLetters[$site][$letter][] = $row;
                }
            }
        } else {
            $groupedAndSplitOnLetters[$site][$category] = $rows;
        }
    }
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
$tree = toD3Structure($groupedAndSplitOnLetters);

echo json_encode(array('name' => 'MOOCs', 'children' => $tree));
//echo "\n\n\n";
//print_r($groupedAndSplitOnLetters);