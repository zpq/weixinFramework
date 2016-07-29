<?php

namespace app\utils;

/**
 * Description of AppConfig
 *
 * @author Administrator
 */
class AppConfig {
    
    //get config by key or all of the config
    public static function get($key = null) {
        $appConfigPath = APPPATH .'/config/app.php';
        $config = include_once($appConfigPath);
        if ($key) {
            return isset($config[$key]) ? $config[$key] : null;
        } else {
            return $config;
        }
    }

    //dynamic setting, won't modify the config file
    public static function set($key, $value) {
        if (!$key) {
            return false;
        }
        $appConfigPath = APPPATH .'/config/app.php';
        $config = include_once($appConfigPath);
        if (isset($config[$key])) {
            $config[$key] = $value;
            return true;
        }
        return false;
    }
    
}

?>
