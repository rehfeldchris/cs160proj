<?php

interface CourseAttributes
{
    /**
     * @return array, like ['name' => 'john doe'. 'image' => 'http://....']
     */
    public function getPrimaryProfessor();
    
    /**
     * @return array of array, with the subarrays like ['name' => 'john doe'. 'image' => 'http://....']
     */
    public function getProfessors();
    
    /**
     * @return string
     */
    public function getCourseName();
    
    /**
     * @return string of plain text. no html, but may have line breaks.
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