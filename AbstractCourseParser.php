<?php


/**
 * Base class for edx and coursera detailed info page parsers.
 * Has some getters, and defines what data constitues a valid parser.
 * 
 * @author Chris Rehfeld
 */


require_once 'Parser.php';
require_once 'CourseAttributes.php';
require_once 'IllegalStateException.php';
require_once 'CourseParsingException.php';



abstract class AbstractCourseParser implements Parser, CourseAttributes
{
    protected $primaryProfessor
            , $otherProfessors
            , $courseName
            , $shortCourseDescription
            , $longCourseDescription
            , $coursePhotoUrl
            , $categoryNames
            , $startDate
            , $endDate
            , $duration
            , $workload
            , $universityName
            , $isParsed
            , $courseVideoUrl
            
            ;
    

            
    /**
     * Makes an educated guess as to whether or not the getters will return valid data.
     * This should be a very good litmus test for whether or not parsing was successful.
     * 
     * An invalid parser requires immediate investigation, 
     * because it's likely caused by the remote website changing their data format.
     * 
     * @return boolean
     */
    public function isValid()
    {
        return $this->isParsed
            && $this->primaryProfessor
            && strlen($this->primaryProfessor['name']) > 2
            && strlen($this->primaryProfessor['name']) < 100
            && strlen($this->courseName) > 0
            && strlen($this->courseName) < 150
            && strlen($this->shortCourseDescription) > 0
            && strlen($this->shortCourseDescription) < 5000
            && strlen($this->longCourseDescription) > 0
            && strlen($this->longCourseDescription) < 10000
            && strlen($this->universityName) > 0
            && strlen($this->universityName) < 150
            && $this->duration < 365
            && strlen($this->coursePhotoUrl) > 0
            && parse_url($this->coursePhotoUrl)
            && (!$this->courseVideoUrl || parse_url($this->courseVideoUrl))
            && parse_url($this->homepageUrl)
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
     * The full course description.
     * 
     * @return string
     */
    public function getLongCourseDescription()
    {
        $this->checkState();
        return $this->longCourseDescription;
    }
    
    /**
     * An small part of the full course description,
     * 
     * @return string
     */
    public function getShortCourseDescription()
    {
        $this->checkState();
        return $this->shortCourseDescription;
    }
    
    /**
     * A url to an image that showcases the course. The image may be any size and type.
     * 
     * @return string
     */
    public function getCoursePhotoUrl()
    {
        $this->checkState();
        return $this->coursePhotoUrl;
    }
    
    /**
     * This can be null for cases like "to be announced"
     * 
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
     * @return int weeks, rounded up to the next whole week
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
     * Returns an array of strings representing category names. 
     * This course belongs to each category in the array.
     * The names are NOT processed or normalized at all.
     * The array can be empty.
     * 
     * @return array of string
     */
    public function getCategoryNames()
    {
        $this->checkState();
        return $this->categoryNames;
    }
    
    /**
     * @return string
     */
    public function getHomepageUrl()
    {
        $this->checkState();
        return $this->homepageUrl;
    }
    
    /**
     * Returns a url to a youtube video
     * 
     * @return string
     */
    public function getVideoUrl()
    {
        $this->checkState();
        return $this->courseVideoUrl; 
    }
    
    /**
     * You cannot call the getters in this object until the values are populated, 
     * which happens when you call parse().
     * 
     * @throws IllegalStateException
     */
    protected function checkState()
    {
        if (!$this->isParsed)
        {
            throw new IllegalStateException(sprintf(
                "must call parse() before calling '%s::%s'"
              , __CLASS__
              , __METHOD__
            ));
        }
    }
}