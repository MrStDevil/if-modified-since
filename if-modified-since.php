<?php

/**
 * If Modified Since
 * 
 * PHP class to handle the `If Modifed Since` functionality.
 * 
 * @link https://www.feedthebot.com/ifmodified.html
 * 
 * @author    Ezra Verheijen <ezra.verheijen@gmail.com>
 * @link      https://github.com/ezraverheijen/if-modified-since
 * @copyright Ezra Verheijen
 * @license   MIT License
 */
class IfModifiedSince
{
    /**
     * Constructor.
     * 
     * Tests if there's a timestamp to work with and if it's a valid Unix timestamp
     * before setting the headers.
     * 
     * @param int|string $mtime Resource last modified time
     */
    public function __construct($mtime)
    {
        if (empty($mtime) || !$this->isValidUnixTimestamp($mtime)) {
            throw new Exception('Missing or invalid timestamp');
        }
        
        $this->setHeaders((int) $mtime);
    }
    
    /**
     * Output the `Modified` headers
     * 
     * @uses http_response_code() if available (PHP >= 5.4)
     * 
     * @param int $mtime Unix timestamp the resource was last modified
     */
    public function setHeaders($mtime)
    {
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
            strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $mtime)
        {
            if (function_exists('http_response_code')) {
                http_response_code(304);
            } else {
                header($this->getHttpProtocol() . ' 304 Not Modified');
            }
            
            exit;
        } else {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
        }
    }
    
    /**
     * Check if a value is a valid Unix timestamp
     * 
     * @param  mixed   $timestamp Value to check
     * @return boolean 
     */
    public function isValidUnixTimestamp($timestamp)
    {
        return (ctype_digit($timestamp) &&
                strtotime(date('d-m-Y H:i:s', $timestamp)) === (int) $timestamp);
    }
    
    public function getHttpProtocol()
    {
        return (!empty($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
    }
}
