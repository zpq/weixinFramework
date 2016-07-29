<?php

namespace app\middleware;
use app\services\CheckWxSignature as Cws;
use \Exception;

class VerifyEchoStr implements IMiddleware {
    
    //if exists param echostr in url, check signature
    public function filter() {
        $echostr = isset($_GET["echostr"]) ? $_GET["echostr"] : null;
        if (!$echostr) {
            return true;
        }
        $cws = new Cws();
        $ret = $cws->valid();
        if ($ret) {
            die($_GET["echostr"]);
        } else {
            throw new Exception('signature valid failed');
        }
    }   
    
}
