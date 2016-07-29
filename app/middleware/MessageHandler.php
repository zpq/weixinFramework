<?php

namespace app\middleware;
use app\services\MessageHandler as Mh;
use app\utils\AppLog;
use app\utils\Xml;
use \Exception;

class MessageHandler implements IMiddleware {

    public function filter() {
        try {
            $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
            $postObj = Xml::x2o($postStr);
            // $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        } catch (Exception $e) {
            AppLog::log($e->getMessage(), 'Exception');
            die($e->getMessage());
        }
        //call messageHandler service
        $mh = new Mh($postObj);
        $mh->handle();

    }

}
