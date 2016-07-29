<?php

namespace app\utils;

class Xml {

    public static function o2x($obj, $root = 'xml') {
        $xml = "<$root>";
        foreach($obj as $key => $v) {
            $xml .= "<$key>$v</$key>";
        }
        $xml.= "</$root>";
        return $xml;
    }

    public static function x2o($xml) {
        return simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    }

}
