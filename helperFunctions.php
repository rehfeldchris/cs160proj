<?php

/**
 *helperFunctions.php gets trending courses, and trending course  from the database
 *and outputs on on the index.php page.
 *
 *@Author  Christopher Rehfeld
 */
 
 /**
  *getTrendingKeywords find trending keywords
  *@parm $dbc, global dbc connection
  *@param $howMany, number of trending keywords to return
  **/
function getTrendingKeywords($dbc, $howMany) {
    $format = "
    select word
      from searched_words
     group
        by word
     order
        by count(*) desc
     limit %d";
    $sql = sprintf($format, $howMany);
    $result = $dbc->query($sql);
    $words = array();
    while ($row = $result->fetch_assoc()) {
        $words[] = $row['word'];
    }
    return $words;
}

 /**
  *getTrendingCourses find trending keywords
  *@parm $dbc, global dbc connection
  *@param $howMany, number of trending keywords to return
  **/
function getTrendingCourses($dbc, $howMany) {
    $format = "
    select id
         , title
      from trendingcourses
    inner
      join course_data
     using (id)
     order
        by hits asc
     limit %d";
    $sql = sprintf($format, $howMany);
    $result = $dbc->query($sql);
    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

 /**
  *recordKeywordSearch records a searched word in searched_words table
  *@param $dbc, global dbc connection
  *@param $word, the string user searched for
  */
function recordKeywordSearch($dbc, $word) {
    $sql = "
        insert into searched_words
        (word, when_searched) values (?, now())
    ";
    $stmt = $dbc->prepare($sql);
    $stmt->bind_param('s', $word);
    $stmt->execute();
}

/**
 *getSearchResults searches for a string in tables
 *$dbc, global connection
 *$words, users searched input
 **/
function getSearchResults($dbc, $words) {

    /**
     * we make dynamic sql like the following if 2 words were entered:
     * ...where (cd.title like concat('%', ?, '%') or cd.short_desc like concat('%', ?, '%') or cd.long_desc like concat('%', ?, '%') or cd.category like concat('%', ?, '%') or d.profname like concat('%', ?, '%'))
            and (cd.title like concat('%', ?, '%') or cd.short_desc like concat('%', ?, '%') or cd.long_desc like concat('%', ?, '%') or cd.category like concat('%', ?, '%') or d.profname like concat('%', ?, '%'))
     * 
     * if there were 3 words, then it would follow the pattern above, but a 3rd line would be present.
     */

    $searchColumns = array('cd.title', 'cd.short_desc', 'cd.long_desc', 'cd.category', 'd.profname');

    $whereClauses = array();
    foreach ($words as $_) {
        $whereClausesForOneColumn = array();
        foreach ($searchColumns as $colName) {
            $whereClausesForOneColumn[] = "$colName like concat('%', ?, '%')";
        }
        $whereClauses[] = sprintf(
            "(%s)"
          , join(' or ', $whereClausesForOneColumn)
        );
    }
    $whereClause = join("\n and ", $whereClauses);

    //we group by id to arbitrarily pick 1 professor per course id
    $sql = "
        select course_image
             , title
             , category
             , start_date
             , course_link
             , id
			 , course_length
             , profname
             , profimage
             , site
             , short_desc
          from coursedetails d
         inner
          join course_data cd
         using (id)
         where $whereClause
         group
            by id";

    $stmt = $dbc->prepare($sql);

    $valsToBind = array();
    foreach ($words as $word) {
        foreach ($searchColumns as $_) {
            $valsToBind[] = addcslashes($word, '_%');
        }
    }

    //php 5.3+ bind_param requires references for some stupid reason... make refs
    $refs = array();
    foreach ($valsToBind as $key => $_) {
        $refs[$key] =& $valsToBind[$key];
    }

    $args = array_merge(array(str_repeat('s', count($refs))), $refs);
    call_user_func_array(array($stmt, 'bind_param'), $args);

    $stmt->execute();


    $rows = array();
    $row = array();
    while ($row = fetch_array($stmt)) {
        $rows[] = $row;
    }
    
    return $rows;
}
/**
 *getAutoSuggestWords, shows auto suggestions as the user types
 *@param $dbc, global connection
 */
function getAutoSuggestWords($dbc) {
    $sql = "
        select title
             , short_desc
             , long_desc
             , category
             , site
          from course_data
    ";
    $result = $dbc->query($sql);
    $words = array();
    while ($row = $result->fetch_assoc()) {
        $words = array_merge(
            $words
          , preg_split('~\W+~', $row['title'])
          , preg_split('~\W+~', strip_tags($row['short_desc']))
          , preg_split('~\W+~', strip_tags($row['long_desc']))
          , preg_split('~\W+~', $row['category'])
          , array($row['site'])
        );
        
    }
    
    $utf8Pattern = '%^(?:
      [\x09\x0A\x0D\x20-\x7E]            # ASCII
    | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
    | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
    | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
    | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
    | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
    | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
    | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
)*$%xs';

    $filteredWords = array();
    foreach (array_unique($words) as $word) {
        //json_encode() is very picky about invalid utf8 characters(it totally fails). well just skip any words with invalid utf8.
        if (strlen($word) > 3 && preg_match($utf8Pattern, $word)) {
            $filteredWords[] = $word;
        }
    }
    return $filteredWords;
}

function fetch_array($stmt) {
    $fields = $out = array();
    foreach($stmt->result_metadata()->fetch_fields() as $field) {
        $fields[] = &$out[$field->name];
    }
    call_user_func_array(array($stmt, 'bind_result'), $fields);
    return $stmt->fetch() ? $out : false;
}
