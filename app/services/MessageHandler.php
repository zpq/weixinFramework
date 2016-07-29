<?php

namespace app\services;
use app\services\msgType\LocationService;
use app\services\keywords\TranslationService;
use app\services\keywords\TelematicsService;
use app\utils\Xml;

/**
 * Description of MessageHandler
 *
 * @author Administrator
 */
class MessageHandler {

    public $postObj;
    public $fromUsername;
    public $toUsername;


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
        /**
        *如果不转成string，from(to)Username变量会是SimpleXMLElement类型，是地址引用的，和this->postObj的属性值一样的内存地址
        * 后面发送信息的时候，交换值的时候会导致fromUsername和toUsername相等
        */
        $this->fromUsername = (string)$this->postObj->FromUserName;
        $this->toUsername = (string)$this->postObj->ToUserName;
    }

    public function handle() {
        $this->messageTypeHandle();
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
            $ls->handle();
        }
    }

    protected function keywordsHandle() {
        $keywords = trim($this->postObj->Content);

        if (mb_substr($keywords, 0, 2, 'UTF-8') == '翻译') {
            $trs = new TranslationService($this->postObj);
            $trs->handle();
        }

        switch ($keywords) {
            case '1':
                $ts = new TelematicsService($this->postObj);
                $ts->handle();
                break;
            case '2':
                $this->sendMessage("请发送你的位置信息");
                break;
            case '3':
                $this->sendMessage("发送格式如下：\"翻译苹果\"\n结果会得到苹果的英文翻译\"apple\"");
                break;
            default:
                //take data from database, match it
                //if there is also no match, return help info to server
                $this->sendMessage("回复相应数字可获得相应服务\n1:查看天气情况\n2:查看周边设施信息\n3:有道翻译");
                break;
        }
    }

    protected function sendMessage($contentString = '') {
        $this->postObj->FromUserName = $this->toUsername;
        $this->postObj->ToUserName = $this->fromUsername;
        $this->postObj->CreateTime = time();
        $this->postObj->Content = $contentString;
        $resultString = Xml::o2x($this->postObj);
        // $resultString = sprintf($this->textTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(), $contentString);
        die($resultString);
    }

}
