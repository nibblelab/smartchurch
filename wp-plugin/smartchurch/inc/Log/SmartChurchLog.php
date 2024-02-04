<?php

namespace SmartChurch\Log;

/**
 * Description of SmartChurchLog
 *
 * @author johnatas
 */
class SmartChurchLog {
    private static $logfile;
    public static $log;
    
    public static function load($file) {
        self::$logfile = $file;
    }
    
    public static function logThis($msg) {
        self::start();
        $log_data = '['.date('d/m/Y H:i:s').'] ' . $msg . "\n";
        fwrite(self::$log, $log_data);
        self::end();
    }
    
    public static function start() {
        self::$log = fopen(self::$logfile, 'a');
    }
    
    public static function end() {
        fclose(self::$log);
    }
}
