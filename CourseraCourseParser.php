<?php

require_once 'AbstractCourseParser.php';

class CourseraCourseParser extends AbstractCourseParser
{
    protected $generalJsonText, $instructorJsonText;
    
    public function __construct($generalJsonText, $instructorJsonText)
    {
        if (strlen($generalJsonText) < 20 || strlen($instructorJsonText) < 20)
        {
            throw new IllegalArgumentException("json text too short");
        }
        $this->generalJsonText = $generalJsonText;
        $this->instructorJsonText = $instructorJsonText;
    }
    
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
        $this->startDate = DateTime::createFromFormat('j F Y', trim($generalObj->courses[0]->start_date_string));
        
        //duration
        if (preg_match('~(\d+) weeks~', $generalObj->courses[0]->duration_string, $matches))
        {
            //convert to days
            $this->duration = $matches[1] * 7;
        }
        
        //calc end date, if possible
        if ($this->startDate && $this->duration)
        {
            $d = clone $this->startDate;
            $d->add(new DateInterval("P{$this->duration}D"));
            $this->endDate = $d;
        }
        
        
        //TODO: professors

    }
}