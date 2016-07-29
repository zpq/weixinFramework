<?php

namespace app\utils;

/**
 * Description of appLog
 *
 * @author Administrator
 */
class AppLog {

    public static function log($logs, $level = array()) {
        if ($level) {
            if (is_array($level)) {
                foreach($level as $lv) {
                    self::write($logs, $lv);
                }
            } else {
                self::write($logs, $level);
            }
        } else {
            $level = 'debug';
            self::write($logs, $level);
        }
    }

    private static function write($logs, $level) {
        $level = strtolower($level);
        $path = LOGPATH . '/' . $level . '_logs';
        $msg = date("Y-m-d H:i:s") . " [$level] : " . $logs . "\r\n";
        try {
            file_put_contents($path, $msg, FILE_APPEND|LOCK_EX);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    //to dolist : log file size limit

}

?>
