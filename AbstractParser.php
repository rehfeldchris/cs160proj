<?php

abstract class AbstractCourseParser implements Parser, CourseAttributes
{
    protected $primaryProfessorName
            , $otherProfessorNames
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
            && strlen($this->primaryProfessorName) > 0
            && strlen($this->courseName) > 0
            && strlen($this->courseDescription) > 0
            && strlen($this->universityName) > 0
            && $this->duration > 0
            && $this->startDate
            ;
    }
    
    /**
     * @return string
     */
    public function getPrimaryProfessorName()
    {
        return $this->primaryProfessorName;
    }
    
    /**
     * @return array of strings
     */
    public function getProfessorNames()
    {
        return $this->otherProfessorNames;
    }
    
    /**
     * @return string
     */
    public function getCourseName()
    {
        return $this->courseName;
    }
    
    /**
     * @return string
     */
    public function getCourseDescription()
    {
        return $this->courseDescription;
    }
    
    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
    
    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
    
    /**
     * @return int days
     */
    public function getDuration()
    {
        return $this->duration;
    }
    
    /**
     * @return int hours per week
     */
    public function getWorkload()
    {
        return $this->workload;
    }
    
    /**
     * @return string
     */
    public function getUniversityName()
    {
        return $this->universityName;
    }
}