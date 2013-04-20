<?php

/**
 * Takes in html source code, and extracts relevant peices of info.
 * This code is specifically for edx.org
 * 
 * 
 * @author Chris Rehfeld
 */


require_once 'AbstractCourseParser.php';
require_once 'phpQuery-onefile.php';

class EdxCourseParser extends AbstractCourseParser
{
    protected $htmlText;
    
    /**
     * Initializes the object, making it ready for the parse() method to be called.
     * 
     * @param type $htmlText the full html source code, utf8 encoded
     */
    public function __construct($htmlText)
    {
        if (!is_string($htmlText))
        {
            throw new InvalidArgumentException("arg1 must be string");
        }
        if (strlen($htmlText) < 20)
        {
            throw new InvalidArgumentException("html text too short");
        }
        $this->htmlText = $htmlText;
    }
    
    /**
     * Trys to extract data. hopefully after this method is called, 
     * the getters should return valid info.
     * 
     * @throws CourseParsingException if something really bad happens
     */
    public function parse()
    {
        //marks that we attempted parsing
        $this->isParsed = true;
        
        //this inits the pq() function, setting the html it will operate on
        phpQuery::newDocument($this->htmlText);
        
        //course start date
        $start = pq('.start-date')->slice(0, 1)->text();
        //format in html is usally Sep 15, 2013
        $this->startDate = DateTime::createFromFormat('M j, Y', trim($start));
        //but sometimes just Sept, 2013
        if (!$this->startDate) 
        {
            $this->startDate = DateTime::createFromFormat('M, Y', trim($start));
        }
        
        //course end date
        $end = pq('.final-date')->slice(0, 1)->text();
        //format in html is usally Sep 15, 2013
        $this->endDate = DateTime::createFromFormat('M j, Y', trim($end));
        //but sometimes just Sept, 2013
        if (!$this->endDate)
        {
            $this->endDate = DateTime::createFromFormat('M, Y', trim($end));
        }
        
        //calc duration, if possible
        if ($this->startDate && $this->endDate)
        {
            $this->duration = (int) $this->startDate->diff($this->endDate)->format('%a');
        }
        
        //staff/professors
        $staff = array();
        foreach (pq('.teacher h3') as $h3elem)
        {
            $name = pq($h3elem)->slice(0, 1)->text();
            $url = pq($h3elem)->prev('div.teacher-image')->find('img')->attr('src');
            $image = null;
            //check for a url. not all teachers have an image
            if ($url)
            {
                //see if the url has a domain
                $parts = parse_url($url);
                //url parsing failure is an exception. it means we scraped a non url(they changed html doc structure)
                if ($parts === false)
                {
                    throw new CourseParsingException("parsing of image url failed. url was: '$url'");
                }
                //add the hostname if its missing
                if (!isset($parts['host']))
                {
                    $url = "https://www.edx.org$url";
                    //make sure new url is well formed
                    if (false === parse_url($url))
                    {
                        throw new CourseParsingException("parsing of constructed image url failed. url was: '$url");
                    }
                }
                
                $image = $url;
            }
            $staff[] = compact('name', 'image');
        }
        $this->otherProfessors = $staff;
        $this->primaryProfessor = $staff[0];
        
        //university name
        $this->universityName = pq('hgroup h1 a')->slice(0, 1)->text();
        
        //course name
        $this->courseName = pq('hgroup h1')->clone()->children()->remove()->end()->text();
        
        //workload
        $effort = pq('p:contains("Estimated Effort")')->next('.start-date')->slice(0, 1)->text();
        if ($effort && preg_match('~\d+~', $effort, $matches))
        {
            $this->workload = (int) $matches[0];
        }
        
        //description
        $this->courseDescription = pq('section.about p')->text();
        
    }
}