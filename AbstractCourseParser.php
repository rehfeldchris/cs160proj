<?php

require_once 'Parser.php';
require_once 'CourseAttributes.php';
require_once 'IllegalStateException.php';
require_once 'CourseParsingException.php';


abstract class AbstractCourseParser implements Parser, CourseAttributes
{
    protected $primaryProfessor
            , $otherProfessors
            , $courseName
            , $courseDescription
            , $startDate
            , $endDate
            , $duration
            , $workload
            , $universityName
            , $isParsed;
            
    public function isValid()
    {
        return $this->isParsed
            && $this->primaryProfessor
            && strlen($this->primaryProfessor['name']) > 2
            && strlen($this->primaryProfessor['name']) < 100
            && strlen($this->courseName) > 0
            && strlen($this->courseName) < 150
            && strlen($this->courseDescription) > 0
            && strlen($this->courseDescription) < 2000
            && strlen($this->universityName) > 0
            && strlen($this->universityName) < 150
            && $this->duration > 0
            && $this->duration < 365
            && $this->startDate
            ;
    }
    
    /**
     * @return array, like ['name' => 'john doe'. 'image' => 'http://....']
     */
    public function getPrimaryProfessor()
    {
        $this->checkState();
        return $this->primaryProfessor;
    }
    
    /**
     * @return array of array, with the subarrays like ['name' => 'john doe'. 'image' => 'http://....']
     */
    public function getProfessors()
    {
        $this->checkState();
        return $this->otherProfessors;
    }
    
    /**
     * @return string
     */
    public function getCourseName()
    {
        $this->checkState();
        return $this->courseName;
    }
    
    /**
     * @return string
     */
    public function getCourseDescription()
    {
        $this->checkState();
        return $this->courseDescription;
    }
    
    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        $this->checkState();
        return $this->startDate;
    }
    
    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        $this->checkState();
        return $this->endDate;
    }
    
    /**
     * @return int days
     */
    public function getDuration()
    {
        $this->checkState();
        return $this->duration;
    }
    
    /**
     * @return int hours per week
     */
    public function getWorkload()
    {
        $this->checkState();
        return $this->workload;
    }
    
    /**
     * @return string
     */
    public function getUniversityName()
    {
        $this->checkState();
        return $this->universityName;
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