<?php


/**
 * this function hash password
 * @param $pass
 * @return string
 */
function hash_mdp($pass):string
{
    $grain1 = "Aezaef";
    $grain2 = "qFgw";
    $grain3 = "fedf";
    $pass = $grain1 . sha1($pass . $grain3) . $grain2;
    $pass = sha1($pass);
    return $pass;
}

/**
 * this function secure data enter in api
 * @param $data
 * @return string
 */
function data_secure($data):string
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * this function verify email valid
 * @param $mail
 * @return bool
 */
function verification_mail($mail):bool
{
    return filter_var($mail, FILTER_VALIDATE_EMAIL);
}

/**
 * this function secure name colum for gestion. for stop Injection SQL
 * @param string $gestion_name
 * @return bool
 */
function verification_gestion_name(string $gestion_name):bool
{
    return ($gestion_name === "v_vetement" || $gestion_name === "v_boisson" || $gestion_name === "c_particulier" || $gestion_name === "c_pilate" || $gestion_name === "c_crosstrainning");
}

/**
 * this function secure name colum for user. for stop Injection SQL
 * @param string $name_column
 * @return bool
 */
function verification_update_user(string $name_column):bool
{
    return $name_column === "user_name" || $name_column === "password";
}