<?php

namespace app\services;
use app\utils\AppConfig;
use app\utils\AppLog;
use \Exception;

/**
 * Description of checkWxSignature
 *
 * @author Administrator
 */
class CheckWxSignature {
    
    public static function valid() {
        try {
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];

            $token = AppConfig::get('token');
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr);
            $tmpStr = sha1(implode($tmpArr));

            if ($tmpStr == $signature) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::log($e->getMessage(), 'exception');
            return false;
        }
    }
    
}

?>
