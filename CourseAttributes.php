<?php

interface CourseAttributes
{
    /**
     * @return string
     */
    public function getPrimaryProfessorName();
    
    /**
     * @return array of string
     */
    public function getProfessorNames();
    
    /**
     * @return string
     */
    public function getCourseName();
    
    /**
     * @return string
     */
    public function getCourseDescription();
    
    /**
     * @return DateTime
     */
    public function getStartDate();
    
    /**
     * @return DateTime
     */
    public function getEndDate();
    
    /**
     * @return int days
     */
    public function getDuration();
    
    /**
     * @return int hours per week
     */
    public function getWorkload();
    
    /**
     * @return string
     */
    public function getUniversityName();
}