<?php

/**
 * Class for extracting course urls from coursera.org 
 * 
 * @author Tatiana Braginets
 */

require_once 'AbstractUrlsParser.php';
require_once 'simple_html_dom.php';


class CourseraUrlsParser extends AbstractUrlsParser
{
	public function __construct()
    {
        $this->urls = array();
    }
	
	/**
	 * Extracts course urls for coursera.org
	 */
	public function parse() 
	{
		$this->isParsed = true;
		
		$c_arr = json_decode(file_get_html('https://www.coursera.org/maestro/api/topic/list2'), true);
		
		foreach ($c_arr['topics'] as  $value) {
			$this->urls[] = 'https://www.coursera.org/course/' . $value['short_name'];
		}
	}
}

?>