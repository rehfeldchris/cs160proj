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
	private $html_data, $courseShortDesc;
   
   	/**
	 * Inits parser by getting web content of edx.org
	 */
    public function __construct()
    {
		$this->urls = array();
		$this->urlsToImages = array();
		$this->courseShortDesc = array();
		$this->html_data = file_get_html('http://www.edx.org');
    }
	
	/**
	 * Extracts course urls and images for edx.org
	 */
	public function parse() 
	{
		$this->isParsed = true;
		$ids = array();
		if ($this->html_data) {
			foreach($this->html_data->find('article.course') as $a) {
				$url = "https://www.edx.org/courses/" . $a->id . '/about';
				$this->urls[] = $url;
				$image = $this->html_data->find('article[id=' . $a->id . '] div.cover-image img');
				$desc = $this->html_data->find('article[id=' .$a->id . '] div.desc p text');
				$this->courseImages[$url] = "https://www.edx.org" . $image[0]->src;
				$this->courseShortDesc[$url] = $desc[0];
				
			}
		}	
	}
	
	/**
	 * 
     * @return string short description for a course
     */
    public function getCourseShortDesc($url)
    {
        $this->checkState();
        return $this->courseShortDesc[$url];
    }
}

?>