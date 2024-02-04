<?php

/**
 * Lib to record logs into a logfile
 * 
 * @author johnatas
 */
class NblLogUtil
{
    /**
     * Record the log into the logfile
     * 
     * @param string $log_file logfile path
     * @param string $str string with the log to be recorded
     * @return void
     */
    public static function log($log_file, $str)
    {
        $timestamp = '[' . date('d/m/Y H:i:s') . '] ';
        file_put_contents($log_file, $timestamp . $str . "\n", FILE_APPEND);
    }
}