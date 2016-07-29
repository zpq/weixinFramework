<?php

define('TOKEN', 'YYT');
// echo(TOKEN);
$wx = new weixin();

if ($_GET['echostr']) {
    $wx->valid();
} else {
    $wx->responseMsg();
}

class weixin {

    public function valid() {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function responseMsg() {

        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)) {

            global $postObj;

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

            global $fromUsername;

            $fromUsername = $postObj->FromUserName;

            global $toUsername;

            $toUsername = $postObj->ToUserName;

            $keyword = trim($postObj->Content);

            $time = time();

            //通过openid获取用户基本信息 功能需要微信高级接口 暂时无法使用
            //https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN
            global $textTpl;

            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[text]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
						</xml>";

            //关注时 回复事件      
            //获取消息类型
            $MsgType = $postObj->MsgType;

            if ($MsgType == 'event') {
                //判断是否关注
                if ($postObj->Event == 'subscribe') {

                    $contentStr = "谢谢你关注了我！\n回复相应数字可获得相应服务\n1:查看天气情况\n2:查看周边设施信息\n3:有道翻译";

                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $contentStr);

                    echo $resultStr;
                    exit;
                }
            }

            if ($MsgType == 'location') {
                echo $this->hotel();
            }

            //翻译
            if (mb_substr($keyword, 0, 2, 'UTF-8') == '翻译') {
                echo $this->translation($keyword);
                exit;
            }

            //逻辑控制
            switch ($keyword) {
                case '1':
                    echo $this->telematics();
                    break;
                case '2':
                    $data2 = "请发送你的位置信息";
                    echo $this->common($data2);
                    break;
                case '3':
                    $data3 = "发送格式如下：\"翻译苹果\"\n结果会得到苹果的英文翻译\"apple\"";
                    echo $this->common($data3);
                    break;
                default:
                    $defaultStr = "回复相应数字可获得相应服务\n1:查看天气情况\n2:查看周边设施信息\n3:有道翻译";
                    echo $this->common($defaultStr);
                    break;
            }
        }
    }

    //共用函数
    function common($data) {
        global $fromUsername, $toUsername, $textTpl;
        return $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $data);
    }

    //周边设施信息
    function hotel() {

        global $fromUsername, $toUsername, $textTpl, $postObj;

        //判断是否是位置类型的
        $Location_X = $postObj->Location_X;

        $Location_Y = $postObj->Location_Y;

        $Scale = $postObj->Scale;

        $Label = $postObj->Label;

        $urlstr = "http://api.map.baidu.com/place/v2/search?&query=酒店&location=" . $Location_X . "," . $Location_Y . "&radius=500&output=json&ak=4Zeg9zMHW4wMBEag8pt8DYFW";

        $jsonstr = file_get_contents($urlstr);

        $jsonContent = json_decode($jsonstr, true);

        $pic_640 = "http://api.map.baidu.com/staticimage?width=640&height=300&center=" . $Location_Y . "," . $Location_X . "&zoom=15&markers=" . $Location_Y . "," . $Location_X . "&markerStyles=l,c";

        $pic_80 = "http://api.map.baidu.com/staticimage?width=80&height=80&center=" . $Location_Y . "," . $Location_X . "&zoom=15&markers=" . $Location_Y . "," . $Location_X . "&markerStyles=l,c";

        $p_640 = file_get_contents($pic_640);

        file_put_contents('./images/640_' . $fromUsername . ".png", $p_640);

        $p_80 = file_get_contents($pic_80);

        file_put_contents('./images/80_' . $fromUsername . ".png", $p_80);

        $ContentArr = $jsonContent['results'];

        $Xmlstr = "<xml>
		 <ToUserName><![CDATA[" . $fromUsername . "]]></ToUserName>
		 <FromUserName><![CDATA[" . $toUsername . "]]></FromUserName>
		 <CreateTime>" . time() . "</CreateTime>
		 <MsgType><![CDATA[news]]></MsgType>
		 <ArticleCount>" . count($ContentArr) . "</ArticleCount>
		 <Articles>";

        foreach ($ContentArr as $k => $v) {
            if ($k == 0) {
                $picurl = "http://sheaned.500yun.pw/images/640_" . $fromUsername . ".png";
            } else {
                $picurl = "http://sheaned.500yun.pw/images/80_" . $fromUsername . ".png";
            }
            $Xmlstr .="
			 <item>
			 <Title><![CDATA[" . $v['name'] . " 地址：" . $v['address'] . " 电话:" . $v['telephone'] . "]]></Title> 
			 <Description><![CDATA[" . $v['name'] . " 地址：" . $v['address'] . " 电话:" . $v['telephone'] . "]]></Description>
			 <PicUrl><![CDATA[picurl]]></PicUrl>
			 <Url><![CDATA[http://api.map.baidu.com/place/detail?uid=" . $v['uid'] . "&output=html&src=" . $v['name'] . "&output=html]]></Url>
			 </item>";
        }

        return $Xmlstr .= "</Articles></xml>";

        //echo $Xmlstr;
    }

    //翻译功能
    function translation($keyword) {
        global $fromUsername, $toUsername, $textTpl;
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

        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $contentStr);

        //echo $resultStr;
        return $resultStr;
    }

    //weather cast
    function telematics() {

        global $fromUsername, $toUsername, $textTpl;

        $telematicsUrl = "http://api.map.baidu.com/telematics/v3/weather?location=上海&output=json&ak=4Zeg9zMHW4wMBEag8pt8DYFW";

        //获取json数据
        $telematicsdata1 = file_get_contents($telematicsUrl);

        //把json转换成数组
        $telematicsdata2 = json_decode($telematicsdata1, true);

        $telematicsdata3 = $telematicsdata2['results'][0];

        $telematicsdata = $telematicsdata3['currentCity'] . "、" . $telematicsdata2['date'] . "\n";

        //循环输出weather_data里的数据
        foreach ($telematicsdata3['weather_data'] as $k => $v) {

            $telematicsdata.= "\n" . $v['date'] . "\n" . $v['weather'] . "\n" . $v['wind'] . "\n" . $v['temperature'] . "\n";
        }

        return $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $telematicsdata);
    }

}

?>