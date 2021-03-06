<?php

/**
 * Defines the common getters that both edx and coursera parsers will provide.
 * 
 * This doesn't neccesarily mean valid data will be returned by all the getters.
 * eg, sometimes a weekly workload isnt listed for the course.
 * 
 * @author Chris Rehfeld
 */



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
    public function getLongCourseDescription();
    
    /**
     * @return string of plain text. no html, but may have line breaks. 
     */
    public function getShortCourseDescription();
    
    /**
     * @return string url to a photo that showcases the course.
     */
    public function getCoursePhotoUrl();
    
    /**
     * @return array of strings
     */
    public function getCategoryNames();
    
    /**
     * @return DateTime
     */
    public function getStartDate();
    
    /**
     * @return DateTime
     */
    public function getEndDate();
    
    /**
     * @return int weeks, rounded up to the next whole week
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
    
    /**
     * @return string
     */
    public function getHomepageUrl();
    
    /**
     * @return string
     */
    public function getVideoUrl();
}