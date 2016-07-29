<?php

namespace app\services;
use app\services\msgType\LocationService;
use app\services\keywords\TranslationService;
use app\services\keywords\TelematicsService;

/**
 * Description of MessageHandler
 *
 * @author Administrator
 */
class MessageHandler {

    private $postObj;

    private $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                       </xml>";

    public static $MESSAGE_TYPE_TEXT = 'text';
    public static $MESSAGE_TYPE_IMAGE = 'image';
    public static $MESSAGE_TYPE_VOICE = 'voice';
    public static $MESSAGE_TYPE_VIDEO = 'video';
    public static $MESSAGE_TYPE_LOCATION = 'location';
    public static $MESSAGE_TYPE_LINK = 'link';
    public static $MESSAGE_TYPE_EVENT = 'event';
    public static $MESSAGE_TYPE_SUBSCRIBE = 'subscribe';


    public function __construct($postStr) {
        $this->postObj = $postStr;
    }

    public function handle() {
        $this->messageTypeHandle();
//        $this->keywordsHandle();
    }

    protected function messageTypeHandle() {
        $MsgType = $this->postObj->MsgType;

        if ($MsgType == self::$MESSAGE_TYPE_EVENT) {
            switch ($this->postObj->Event) {
                case self::$MESSAGE_TYPE_SUBSCRIBE:
                    $contentString = "谢谢你关注了我！\n回复相应数字可获得相应服务\n1:查看天气情况\n2:查看周边设施信息\n3:有道翻译";
                    die($this->sendMessage($contentString));
                    break;
                default:
                    break;
            }
        } elseif ($MsgType == self::$MESSAGE_TYPE_TEXT) {
             $this->keywordsHandle();
        } elseif ($MsgType == self::$MESSAGE_TYPE_LOCATION) {
            $ls = new LocationService($this->postObj);
            die($ls->handle());
        }



//        if ($MsgType == 'event') {
//            //判断是否关注
//            if ($this->postObj->Event == 'subscribe') {
//                $contentString = "谢谢你关注了我！\n回复相应数字可获得相应服务\n1:查看天气情况\n2:查看周边设施信息\n3:有道翻译";
//                die($this->sendMessage($contentString));
//            }
//        }
//
//        if ($MsgType == 'location') {
//            $ls = new LocationService($this->postObj);
//            die($ls->handle());
//        }
    }

    protected function keywordsHandle() {
        $keywords = $this->postObj->Content;

        if (mb_substr($keywords, 0, 2, 'UTF-8') == '翻译') {
            $trs = new TranslationService($this->postObj);
            die($trs->handle());
        }

        switch ($keywords) {
            case '1':
                $ts = new TelematicsService($this->postObj);
                die($ts->handle());
                break;
            case '2':
                die($this->sendMessage("请发送你的位置信息"));
                break;
            case '3':
                die($this->sendMessage("发送格式如下：\"翻译苹果\"\n结果会得到苹果的英文翻译\"apple\""));
                break;
            default:
                //take data from database, match it
                //if there is also no match, return help info to server
                die($this->sendMessage("回复相应数字可获得相应服务\n1:查看天气情况\n2:查看周边设施信息\n3:有道翻译"));
                break;
        }
    }

    protected function sendMessage($contentString = '') {
        $resultString = sprintf($this->textTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(), $contentString);
        return $resultString;
    }

}
