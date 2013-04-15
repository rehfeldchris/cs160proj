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
	private $html_data;
   
   	/**
	 * Inits parser by getting web content of edx.org
	 */
    public function __construct()
    {
		$this->urls = array();
		$this->html_data = file_get_html('http://www.edx.org');
    }
	
	/**
	 * Extracts course urls for edx.org
	 */
	public function parse() 
	{
		$this->isParsed = true;
		
		if ($this->html_data) {
			foreach($this->html_data->find('article.course') as $a) {
				$this->urls[] = "https://www.edx.org/courses/" . $a->id . '/about';
			}
		}	
	}
}

?>