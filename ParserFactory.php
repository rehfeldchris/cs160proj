<?php

/**
 * This class takes a url which points to the detailed info on a course. 
 * It will create an appropriate parser(coursera or edx) and return it.
 * It makes network requests so that it can fetch the relevant html
 *  or json for the course, to give to the parser.
 * 
 * @author Chris Rehfeld
 */
 
require_once 'EdxCourseParser.php';
require_once 'CourseraCourseParser.php';
require_once 'UrlFetcher.php';

class ParserFactory
{
    /**
     * 
     * 
     * @param string $url a url for a detailed info page for a specific course on either edx or coursera
     * @param array $extraInfo any additional info the parser may need. eg, edx needs a shortCourseDescription
     * @return \EdxCourseParser|\CourseraCourseParser
     * @throws RuntimeException if the url doesn't seems like its for edx or coursera
     */
    public function create($url, $extraInfo = array()) 
    {
        // this should only be set to true during development, never during production. it will give stale results.
        $useCachedHttpResponses = true;
        if (false !== stripos($url, 'edx.org/'))
        {
            return new EdxCourseParser($url, UrlFetcher::fetch($url, $useCachedHttpResponses), $extraInfo['shortCourseDescription']);
        }
        elseif (false !== stripos($url, 'coursera.org/'))
        {
            //the course short name is interactivepython in 'https://www.coursera.org/course/interactivepython';
            $path = parse_url($url, PHP_URL_PATH);
            if ($path === false)
            {
                throw new RuntimeException("couldnt parse url: '$url'");
            }
            $parts = explode('/', $path);
            if (count($parts) < 3)
            {
                throw new RuntimeException("unexpected url format. url: '$url'");
            }
            $courseShortName = $parts[2];
            $generalJsonUrl = 'https://www.coursera.org/maestro/api/topic/information?topic-id=' . rawurlencode($courseShortName);
            $instructorJsonUrl = 'https://www.coursera.org/maestro/api/user/instructorprofile?excelude_topics=1&topic_short_name=' . rawurlencode($courseShortName);
            $genJson = UrlFetcher::fetch($generalJsonUrl, $useCachedHttpResponses);
            $instructorJson = UrlFetcher::fetch($instructorJsonUrl, $useCachedHttpResponses);
            return new CourseraCourseParser($url, $genJson, $instructorJson);
        }
        else
        {
            throw new RuntimeException("url isnt coursera or edx. url: '$url'");
        }
    }
}