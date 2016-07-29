<?php

namespace app\services\keywords;

/**
 * Description of TelematicsService
 *
 * @author Administrator
 */
class TelematicsService {

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

        $telematicsUrl = "http://api.map.baidu.com/telematics/v3/weather?location=上海&output=json&ak=4Zeg9zMHW4wMBEag8pt8DYFW";

        //获取json数据
        $telematicsdata1 = file_get_contents($telematicsUrl);

        //把json转换成数组
        $telematicsdata2 = json_decode($telematicsdata1, true);

        $telematicsdata3 = $telematicsdata2['results'][0];

        $telematicsdata = $telematicsdata3['currentCity'] . "、" . $telematicsdata2['date'] . "\n";

        foreach ($telematicsdata3['weather_data'] as $k => $v) {

            $telematicsdata.= "\n" . $v['date'] . "\n" . $v['weather'] . "\n" . $v['wind'] . "\n" . $v['temperature'] . "\n";
        }

        return $resultStr = sprintf($this->textTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(), $telematicsdata);
    }
}

?>
