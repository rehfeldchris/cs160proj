<?php

/**
 * Base class for edx and coursera urls parser
 * 
 * @author Tatiana Braginets
 */

require_once 'Parser.php';
require_once 'IllegalStateException.php';


abstract class AbstractUrlsParser implements Parser
{
    protected $urls,$courseImages, $isParsed;
	
	/**
     * Tests if array is populated after parsing
     * 
     * @return boolean
     */
	public function isValid()
    {
        return $this->isParsed && $this->urls && sizeof($this->urls) > 0;
	}
	
	/**
	 * 
     * @return array of course urls, each entry is in format 
	 * 'https://www.coursera.org/course/...'
	 * or 'https://www.edx.org/courses/...'
     */
    public function getUrls()
    {
        $this->checkState();
        return $this->urls;
    }
	
	/**
	 * 
     * @return string image url for a course
     */
    public function getCourseImage($url)
    {
        $this->checkState();
        return $this->courseImages[$url];
    }
	
	/**
     * You cannot call the getters in this object until the values are populated, 
     * which happens when you call parse().
     * 
     * @throws IllegalStateException
	 */
    protected function checkState()
    {
        if (!$this->isParsed) {
            throw new IllegalStateException(sprintf(
                "must call parse() before calling '%s::%s'"
              , __CLASS__
              , __METHOD__
            ));
        }
    }
}

?>