<?php

class Tools
{
    public static function msg_argument(string $msg)
    {
        throw new Exception($msg);
    }
    
    public static function sendMail($destinataire, $sujet, $message):void
    {
        $headers = "From: xxxxx@gmail.com";
        mail($destinataire,$sujet,$message,$headers);
    }
}