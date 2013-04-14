<?php

/**
 * Class for extracting course urls from edx.org
 * 
 * @author Tatiana Braginets
 */

require_once 'AbstractUrlsParser.php';
require_once 'simple_html_dom.php';

class EdxUrlsParser extends AbstractUrlsParser
{
   
    public function __construct()
    {
		$this->urls = array();
    }
	
	/**
	 * Extracts course urls for edx.org
	 */
	public function parse() 
	{
		$this->isParsed = true;
		
		$html_data = file_get_html('http://www.edx.org');
		
		foreach($html_data->find('article.course') as $a) {
			$this->urls[] = "https://www.edx.org/courses/" . $a->id . '/about';
			
		}
	}
}

?>