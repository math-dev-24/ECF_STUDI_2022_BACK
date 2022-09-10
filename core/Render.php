<?php

class Render
{
    static function sendJSON($infos): void
    {
        echo json_encode($infos, JSON_UNESCAPED_UNICODE);
        exit();
    }
    static function sendJsonError($error):void
    {
        $data = ["error" => $error];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
    static function sendJsonOK():void
    {
        echo json_encode(['ok'=>'ok'], JSON_UNESCAPED_UNICODE);
        exit();
    }

}