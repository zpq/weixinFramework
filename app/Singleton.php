<?php

/**
 * 1. verify wx echostr
 * 2. handle client message
 * 3. call service to do something
 */

namespace app;
use \Exception;
use app\utils\AppLog;

class Singleton {
    
    private $middlewares = [];
    
    public function __construct() {
        AppLog::log("app init...", 'info');
    }
    
    // run middleware by register order
    public function run () {
        AppLog::log("app is running...", 'info');
        $this->middlewares = include_once APPPATH . '/config/middleware.php';
        try {
            foreach($this->middlewares as $middleware) {
                $object = new $middleware;
                $object->filter();
            }
        } catch (Exception $e) {
            AppLog::log($e->getMessage(), 'exception');
            die($e->getMessage());
        }
    }

    
    //use IOC
//    public function run () {
//        AppLog::log("app is running...", 'info');
//        $this->middlewares = include_once APPPATH . '/config/middleware.php';
//        try {
//            foreach($this->middlewares as $key => $middleware) {
//                $object = new $middleware;
//                $object->filter();
//            }
//        } catch (Exception $e) {
//            AppLog::log($e->getMessage(), 'exception');
//            die($e->getMessage());
//        }
//    }
    
    //use container get
    public static function get() {
        
    }
    
    //use container set
    public static function set() {
        
    }


    
}





