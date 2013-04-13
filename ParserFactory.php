<?php

require_once 'EdxCourseParser.php';
require_once 'CourseraCourseParser.php';


class ParserFactory
{
    public function create($url)
    {
        if (false !== strpos($url, 'edx.org/')) {
            return new EdxCourseParser(file_get_contents($url));
        } elseif (false !== strpos($url, 'coursera.org/')) {
            //the course short name is interactivepython in 'https://www.coursera.org/course/interactivepython';
            $path = parse_url($url, PHP_URL_PATH);
            if ($path === false) {
                throw new RuntimeException("couldnt parse url: '$url'");
            }
            $parts = explode('/', $path);
            if (count($parts) < 3) {
                throw new RuntimeException("unexpected url format. url: '$url'");
            }
            $courseShortName = $parts[2];
            $generalJsonUrl = 'https://www.coursera.org/maestro/api/topic/information?topic-id=' . rawurlencode($courseShortName);
            $instructorJsonUrl = 'https://www.coursera.org/maestro/api/user/instructorprofile?excelude_topics=1&topic_short_name=' . rawurlencode($courseShortName);
            $genJson = file_get_contents($generalJsonUrl);
            $instructorJson = file_get_contents($instructorJsonUrl);
            return new CourseraCourseParser($genJson, $instructorJson);
        } else {
            throw new RuntimeException("url isnt coursera or edx. url: '$url'");
        }
    }
}