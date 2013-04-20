<?php

/**
 * Takes in json text, and extracts relevant peices of info.
 * This code is specifically for coursera.org
 * 
 * @author Chris Rehfeld
 */

require_once 'AbstractCourseParser.php';



class CourseraCourseParser extends AbstractCourseParser
{
    protected $generalJsonText
            , $instructorJsonText;
    
    /**
     * Initializes the object, making it ready for the parse() method to be called.
     * 
     * @param string $generalJsonText the json text that contains the general info about the course
     * @param string $instructorJsonText the json text that contains info about the professors for the course
     * @throws IllegalArgumentException
     */
    public function __construct($homepageUrl, $generalJsonText, $instructorJsonText)
    {
        $this->homepageUrl = $homepageUrl;
        
        if (!is_string($generalJsonText) || !is_string($instructorJsonText))
        {
            throw new InvalidArgumentException("arg 1 and 2 must be string");
        }
        if (strlen($generalJsonText) < 20 || strlen($instructorJsonText) < 20)
        {
            throw new InvalidArgumentException("json text too short");
        }
        $this->generalJsonText = $generalJsonText;
        $this->instructorJsonText = $instructorJsonText;
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
        
        $generalObj = json_decode($this->generalJsonText);
        $err = json_last_error();
        //a common error is not being utf8 encoded, 
        //but this also catches syntax and other errors
        if ($err !== JSON_ERROR_NONE)
        {
            throw new RuntimeException("json parsing failed, error code $err");
        }
        
        $instructorObj = json_decode($this->instructorJsonText);
        $err = json_last_error();
        //a common error is not being utf8 encoded, 
        //but this also catches syntax and other errors
        if ($err !== JSON_ERROR_NONE)
        {
            throw new RuntimeException("json parsing failed, error code $err");
        }
        
        //course description
        $this->courseDescription = strip_tags($generalObj->about_the_course);
        
        //university name
        $this->universityName = $generalObj->universities[0]->name;
        
        //course name
        $this->courseName = $generalObj->name;
        
        //workload
        if (preg_match('~(\d+)(\-(\d+)) hours?/week~', $generalObj->estimated_class_workload, $matches))
        {
            if (isset($matches[3]))
            {
                // case like: 5 hours/week
                $ave = ceil(($matches[1] + $matches[3] ) / 2);
            }
            else
            {
                // case like 5-6 hours/week
                $ave = (int) $matches[1];
            }
            if ($ave < 0 || $ave > 100)
            {
                throw new CourseParsingException("parsing of workload failed for $ave '{$generalObj->estimated_class_workload}'");
            }
            $this->workload = $ave;
        }
        
        
        
        //start date
        $course = $generalObj->courses[0];
        if ($course->start_date_string)
        {
            $this->startDate = DateTime::createFromFormat('j F Y', trim($course->start_date_string));
        }
        else 
        {
            $this->startDate = DateTime::createFromFormat('Y n d', "{$course->start_year} {$course->start_month} {$course->start_day}");
        }
        
        //duration
        if (preg_match('~(\d+) weeks~', $generalObj->courses[0]->duration_string, $matches))
        {
            //convert weeks to days
            $this->duration = $matches[1] * 7;
        }
        
        //calc end date, if possible
        if ($this->startDate && $this->duration)
        {
            $d = clone $this->startDate;
            $d->add(new DateInterval("P{$this->duration}D"));
            $this->endDate = $d;
        }
        
        
        //staff/professors
        $staff = array();
        foreach ($instructorObj as $prof) 
        {
            $name = strlen($prof->middle_name)
                  ? "{$prof->first_name} {$prof->middle_name} {$prof->last_name}"
                  : "{$prof->first_name} {$prof->last_name}";
            $image = $prof->photo;
            $staff[] = compact('name', 'image');
        }
        $this->otherProfessors = $staff;
        $this->primaryProfessor = $staff[0];
        
        
        //categories
        $categoryNames = array();
        foreach ($generalObj->categories as $category)
        {
            $categoryNames[] = $category->name;
        }
        $this->categoryNames = $categoryNames;
        
        //short description
        $this->shortCourseDescription = $generalObj->short_description;
        
        //long description
        $this->longCourseDescription = $generalObj->about_the_course;
        
        //video url
        if (strlen($generalObj->video) > 0)
        {
            if (!preg_match('~^[a-zA-Z0-9_-]{5,50}$~Di', $generalObj->video))
            {
                throw new CourseParsingException("Unexpected youtube url component format. val='{$generalObj->video}'");
            }
            else
            {
                $this->courseVideoUrl = 'http://www.youtube.com/watch?v=' . urlencode($generalObj->video);
            }
        }
        
        
        //photo url
        $photoUrl = $generalObj->photo;
        if (strlen($photoUrl) > 0)
        {
            $parts = parse_url($photoUrl);
            if (isset($parts['host']))
            {
                $this->coursePhotoUrl = $photoUrl;
            }
        }
       
        
        

    }
}