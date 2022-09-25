<?php

class Tools
{
    public static function sendMail($destinataire, $sujet, $message):void
    {
        $headers = "From: xxxxx@gmail.com";
        mail($destinataire,$sujet,$message,$headers);
    }

    /**
     * this function hash password
     * @param string $pass
     * @return string
     */
    public static function hashMdp(string $pass) : string
    {
        $grain1 = "Aezaef";
        $grain2 = "qFgw";
        $pass = $grain1 . sha1($pass . $grain2) . $grain2;
        return sha1($pass);
    }

    /**
     * this function secure data enter in api
     * @param string|int $data
     * @return string
     */
    public static function dataSecure(string|int $data):string
    {
        $data = trim($data);
        $data = stripslashes($data);
        return htmlspecialchars($data);
    }

    /**
     * this function verify email valid
     * @param string $mail
     * @return bool
     */
    public static function verificationEmail(string $mail):bool
    {
        return filter_var($mail, FILTER_VALIDATE_EMAIL);
    }

    /**
     * this function secure name colum for gestion. for stop Injection SQL
     * @param string $gestionName
     * @return bool
     */
    public static function verificationGestionName(string $gestionName):bool
    {
        return ($gestionName === "v_vetement" || $gestionName === "v_boisson" || $gestionName === "c_particulier" || $gestionName === "c_pilate" || $gestionName === "c_crosstrainning");
    }

    /**
     * this function secure name colum for user. for stop Injection SQL
     * @param string $nameColumn
     * @return bool
     */
    public static function verificationUpdateUser(string $nameColumn):bool
    {
        return $nameColumn === "user_name" || $nameColumn === "password" || $nameColumn === "first_connect" ||$nameColumn === "user_active" || $nameColumn === "profil_url";
    }
}