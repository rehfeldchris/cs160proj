<?php

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

function recordKeywordSearch($dbc, $word) {
    $sql = "
        insert into searched_words
        (word, when_searched) values (?, now())
    ";
    $stmt = $dbc->prepare($sql);
    $stmt->bind_param('s', $word);
    $stmt->execute();
}














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

    function fetch_array($stmt) {
        $fields = $out = array();
        foreach($stmt->result_metadata()->fetch_fields() as $field) {
            $fields[] = &$out[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $fields);
        return $stmt->fetch() ? $out : false;
    }


    $rows = array();
    $row = array();
    while ($row = fetch_array($stmt)) {
        $rows[] = $row;
    }
    
    return $rows;
}