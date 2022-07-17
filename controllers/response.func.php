<?php

class Tools
{
    public static function return_json(string $etat,array $data)
    {
        return json_encode(
            [
                "etat" => $etat,
                "data" => $data
            ]
        );
    }

    public static function sendMail($destinataire, $sujet, $message):void
    {
        $headers = "From: xxxxx@gmail.com";
        mail($destinataire,$sujet,$message,$headers);
    }
}