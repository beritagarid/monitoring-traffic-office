<?php namespace Beritagar;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class Log
{
    public static $log_level;
    public static $logger;
    public static $logger_start;
    public static $logs_directory = "../apps/storage/logs/";

    public static function init($logname= "Lintas" ,$file_name = 'beritagar',$level = Logger::DEBUG){
        if(isset(self::$logger)){
            $name = self::$logger->getName();
            if($name == $logname){
                return self::$logger;
            }
        }

        if(isset(self::$log_level)){
            $level = self::$log_level;
        }

        $logger = new Logger($logname);
        if(isset(self::$logs_directory)){
            $log_file = self::$logs_directory . $file_name . '.log';
            $logger->pushHandler(new RotatingFileHandler( $log_file, 7, $level));
        }

        $logger->pushProcessor(function ($record) {
            if(isset($record['context']['start'])){
                $record['extra']['milis'] = self::getDuration($record['context']['start']);
                unset($record['context']['start']);
            }
            return $record;
        });

        self::$logger = $logger;

        return $logger;
    }

    public static function start(){
        $start = microtime(true);
        self::$logger_start = $start;
        return $start;
    }

    public static function getDuration($time_start){
        return round((microtime(true) - $time_start) * 1000,2);
    }
}