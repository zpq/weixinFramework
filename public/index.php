<?php

require '../vendor/autoload.php';

//use Illuminate\Database\Capsule\Manager as Capsule;
//$capsule = new Capsule();
//print_r($capsule);

use app\Singleton;
use app\utils\AppConfig;
use app\utils\AppLog;

define("ROOTPATH", str_replace("\\", '/', dirname(dirname(__FILE__))));
define("APPPATH", ROOTPATH . '/app');
define("LOGPATH", APPPATH . '/logs');

$singleton = new Singleton();
$singleton->run();

/*
 $textTpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <FuncFlag>0</FuncFlag>
                <Articles>
                <item>
                    <name>1</name>
                </item>
                <item>
                    <name>2</name>
                </item>
                </Articles>
               </xml>";
 
 
$postObj = simplexml_load_string($textTpl, 'SimpleXMLElement', LIBXML_NOCDATA);
print_r($postObj);

$arr = o2a($postObj);
print_r($arr);

print_r(a2x($arr));

function a2x($array, $root= "xml") {
    $xml = "<$root>";
    foreach($array as $key => $arr) {
        if (is_array($arr)) {
            $xml .= a2x($arr, $key);
        } else {
            $xml .= "<$key>$arr</$key>";
        }
    }
    $xml .= "</$root>";
    return $xml;
}

function o2a($obj) {
    if (is_object($obj)) {
        $obj = (array)$obj;
    }
    if (is_array($obj)) {
        foreach ($obj as $key=>$v) {
            $obj[$key] = o2a($v);
        }
    }
    return $obj;
}
*/


/**
 * some test as follow
 */

/*

//test config
AppConfig::get();
//test ok

//test log
AppLog::log("test log", array('info', 'debug', 'test'));
//test ok

*/







