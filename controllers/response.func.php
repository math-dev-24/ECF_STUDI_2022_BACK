<?php

class Tools
{
    public const TROP_LONG = "Trop d'arguments";
    public const ERR_ARG = "Arguments érronés";
    public const MANQUANT_ARG = "Arguments manquants";
    public const NBR_ARG = "l'id doit être un nombre";

    public static function return_json(string $etat,array $data)
    {
        return json_encode(
            [
                "etat" => $etat,
                "data" => $data
            ]
        );
    }

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