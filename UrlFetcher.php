<?php

/**
 * This class serves 2 purposes.
 * 1- some installs have a bugged openssl module, 
 * and file_get_contents fails on ssl pages due to it. so, curl is used to workaround it.
 * 
 * 2-it can cache http requests. this is useful during development and 
 * testing, to reduce the time needed to test your changes.
 * 
 * @author Chris Rehfeld
 */

class UrlFetcher
{
    /**
     * The directoy where the files will be cached. no trailing slash.
     */
    const CACHE_DIR = './url_request_cache';
    
    /**
     * Retrieves the http response text for the given url. Optionally, try to satisy the request from the cache.
     * 
     * This will always write to the cache.
     * 
     * @param string $url
     * @param boolean $useCached
     * @return mixed string on success, or false on failure.
     */
    public static function fetch($url, $useCached = false)
    {
        if ($useCached)
        {
            $data = self::getFromCache($url);
            if (is_string($data))
            {
                return $data;
            }
        }
        
        $data = self::request($url);
        if (is_string($data))
        {
            self::putIntoCache($url, $data);
        }
        return $data;
    }
    
    /**
     * Performs the http request.
     * 
     * @param string $url
     * @return mixed string on success, or false on failure.
     */
    protected static function request($url)
    {
        //from http://stackoverflow.com/questions/14078182/openssl-file-get-contents-failed-to-enable-crypto
        //use these setting because some ppl have buggy openssl module
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        return curl_exec($ch);
    }
    
    
    protected static function getFromCache($url)
    {
        $file = self::filePath($url);
        return is_readable($file) ? file_get_contents($file) : false;
    }
    
    protected static function filePath($url)
    {
        return self::CACHE_DIR . '/' . urlencode($url);
    }
    
    protected static function putIntoCache($url, $data)
    {
        file_put_contents(self::filePath($url), $data);
    }
}