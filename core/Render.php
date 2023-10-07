<?php

class Render
{
    /**
     * this function return JSON info
     * @param $infos
     * @return void
     */
    static function sendJSON($infos): void
    {
        echo json_encode($infos, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * this function return JSON Error
     * @param $error
     * @return void
     */
    static function sendJsonError($error):void
    {
        $data = ["error" => $error];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * this function return JSON OK
     * @return void
     */
    static function sendJsonOK():void
    {
        echo json_encode(['ok'=>'ok'], JSON_UNESCAPED_UNICODE);
        exit();
    }
}