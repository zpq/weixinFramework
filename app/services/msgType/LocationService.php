<?php

namespace app\services\msgType;

/**
 * Description of Location
 *
 * @author Administrator
 */
class LocationService {

    private $postObj;

    public function __construct($postObj) {
       $this->postObj = $postObj;
    }

    public function handle() {
        $Location_X = $this->postObj->Location_X;

        $Location_Y = $this->postObj->Location_Y;

        $Scale = $this->postObj->Scale;

        $Label = $this->postObj->Label;

        $fromUsername = $this->postObj->FromUserName;
        $toUsername = $this->postObj->ToUserName;


        $urlstr = "http://api.map.baidu.com/place/v2/search?&query=酒店&location=" . $Location_X . "," . $Location_Y . "&radius=500&output=json&ak=4Zeg9zMHW4wMBEag8pt8DYFW";

        $jsonstr = file_get_contents($urlstr);

        $jsonContent = json_decode($jsonstr, true);

        $pic_640 = "http://api.map.baidu.com/staticimage?width=640&height=300&center=" . $Location_Y . "," . $Location_X . "&zoom=15&markers=" . $Location_Y . "," . $Location_X . "&markerStyles=l,c";

        $pic_80 = "http://api.map.baidu.com/staticimage?width=80&height=80&center=" . $Location_Y . "," . $Location_X . "&zoom=15&markers=" . $Location_Y . "," . $Location_X . "&markerStyles=l,c";

        $p_640 = file_get_contents($pic_640);

        file_put_contents(APPPATH . '/public/images/640_' . $fromUsername . ".png", $p_640);

        $p_80 = file_get_contents($pic_80);

        file_put_contents(APPPATH . '/public/images/80_' . $fromUsername . ".png", $p_80);

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
                $picurl = "http://sheaned.com/weixin/images/640_" . $fromUsername . ".png";
            } else {
                $picurl = "http://sheaned.com/weixin/images/80_" . $fromUsername . ".png";
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
    }

}

?>
