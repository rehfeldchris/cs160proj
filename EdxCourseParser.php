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
require_once 'EdxUniversityNameConverter.php';

class EdxCourseParser extends AbstractCourseParser
{
    protected $htmlText;
    
    /**
     * Initializes the object, making it ready for the parse() method to be called.
     * 
     * @param string $htmlText the full html source code, utf8 encoded.
     * @param string $homepageUrl the url to the detailed course description webpage.
     * @param string $shortCourseDescription a short textual description of the course. probably 1 sentence.
     */
    public function __construct($homepageUrl, $htmlText, $shortCourseDescription)
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
        $this->homepageUrl = $homepageUrl;
        $this->shortCourseDescription = $shortCourseDescription;
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
        //but sometimes just Sept, 2013
        $dateStr = trim(str_replace(',', '', $start));
        if (preg_match('~^\w+(\d{1,2} )? \d{2,4}$~', $dateStr))
        {
            $ts = '@' . strtotime($dateStr);
            $this->startDate = date_create($ts);
            // if we still failed...this is an unknown date format
            if (!$this->startDate)
            {
                throw new CourseParsingException("Unknown date format. date_str='{$dateStr}'");
            }
        }
        
        //course end date
        $end = pq('.final-date')->slice(0, 1)->text();
        //format in html is usally Sep 15, 2013
        //but sometimes just Sept, 2013
        $dateStr = trim(str_replace(',', '', $start));
        if (preg_match('~^\w+(\d{1,2} )? \d{2,4}$~', $dateStr))
        {
            $ts = '@' . strtotime($dateStr);
            $this->endDate = date_create($ts);
            // if we still failed...this is an unknown date format
            if (!$this->endDate)
            {
                throw new CourseParsingException("Unknown date format. date_str='{$dateStr}'");
            }
        }
        
        //calc duration, if possible
        if ($this->startDate && $this->endDate)
        {
            // divide diff by seconds per day
            $diffDays = ($this->endDate->getTimeStamp() / $this->startDate->getTimestamp()) / (60 * 60 * 24);
            $this->duration = ceil($diffDays / 7);
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
//        $this->universityName = EdxUniversityNameConverter::convert($this->universityName);
		
        //course name
        $this->courseName = pq('hgroup h1')->clone()->children()->remove()->end()->text();
		
        //workload
        $effort = pq('p:contains("Estimated Effort")')->next('.start-date')->slice(0, 1)->text();
        if ($effort && preg_match('~\d+~', $effort, $matches))
        {
            $this->workload = (int) $matches[0];
        }
        
        //long course description
        $this->longCourseDescription = pq('section.about p')->text();
        
        //category names....but edx doesnt categorize, so we just provide an empty list
        $this->categoryNames = array();
        
        //course photo
        $photoUrl = pq('div.hero img')->slice(0, 1)->attr('src');
        if (strlen($photoUrl) > 0)
        {
            $parts = parse_url($photoUrl);
            if (!$parts)
            {
                throw new CourseParsingException("couldnt parse photo url");
            }
            else
            {
                $this->coursePhotoUrl = isset($parts['host'])
                    ? $photoUrl
                    : "http://www.edx.org" . $photoUrl;
            }
        }
        else
        {
            throw new CourseParsingException("couldnt find photo url");
        }
        
        
        
        
        //course video
        $videoUrl = pq('#video-modal iframe')->slice(0, 1)->attr('src');
        if (strlen($videoUrl) > 0)
        {
            $parts = parse_url($videoUrl);
            if (!$parts)
            {
                throw new CourseParsingException("couldnt parse video url");
            }
            else
            {
                $this->courseVideoUrl = $videoUrl;
            }
        }
        
    }
}