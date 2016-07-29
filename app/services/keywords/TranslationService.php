<?php

namespace app\services\keywords;

/**
 * Description of TranselationService
 *
 * @author Administrator
 */
class TranslationService {

    private $postObj;

    private $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                       </xml>";

    public function __construct($postObj) {
        $this->postObj = $postObj;
    }

    public function handle() {
        $keyword = $this->postObj->Content;
        $fanyi = mb_substr($keyword, 0, 2, 'UTF-8'); //翻译
        $word = str_replace($fanyi, '', $keyword); //去除翻译两个字
        $key = '435084089';
        $keyfrom = 'yytwebsite';
        $url = 'http://fanyi.youdao.com/openapi.do?keyfrom=' . $keyfrom . '&key=' . $key . '&type=data&doctype=json&version=1.1&q=' . urlencode($word); //有道翻译API

        $fanyiJson = file_get_contents($url); //获取url数据,返回Json数据

        $fanyiArr = json_decode($fanyiJson, true); //json 转换成 数组

        $contentStr = "【查询】\n" . $fanyiArr['query'] . "\n【翻译】\n" . $fanyiArr['translation'][0]; //拼接返回给用户的字符串
        //扩展翻译
        if (isset($fanyiArr['web'])) {
            $extension = "\n【扩展翻译】";

            $arr = $fanyiArr['web'][0]['value'];
            $n = 1;
            foreach ($arr as $v) {
                $extension .= "\n" . $n . '、' . $v;
                $n++;
            }
        } else {
            $extension = '';
        }

        $contentStr .= $extension; //拼接扩展查询

        $resultStr = sprintf($this->textTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(), $contentStr);

        return $resultStr;
    }

}

?>
