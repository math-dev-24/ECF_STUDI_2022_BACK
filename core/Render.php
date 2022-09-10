<?php

class Render
{
    static function send_JSON($infos): void
    {
        echo json_encode($infos, JSON_UNESCAPED_UNICODE);
    }
    static function send_JSON_error($info):void
    {
        $data = ["error" => $info];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    static function send_JSON_OK():void
    {
        echo json_encode(['ok'=>'ok'], JSON_UNESCAPED_UNICODE);
    }

}