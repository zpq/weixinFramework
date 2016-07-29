<?php

namespace app\services\keywords;

/**
 * Description of TelematicsService
 *
 * @author Administrator
 */
class TelematicsService app\service\MessageHandler {

    public function __construct() {

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

        $this->postObj->FromUserName = $this->toUsername;
        $this->postObj->ToUserName = $this->fromUsername;
        $this->postObj->CreateTime = time();
        $this->postObj->Content = $telematicsdata;
        $resultString = Xml::o2x($this->postObj);
        die($resultString);
    }
}

?>
