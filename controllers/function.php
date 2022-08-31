<?php

function cryptageMdp($pass)
{
    $grain1 = "Aezaef";
    $grain2 = "qFgw";
    $grain3 = "fedf";
    $pass = $grain1 . sha1($pass . $grain3) . $grain2;
    $pass = sha1($pass);
    return $pass;
}

function dataSecure($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function verification_mail($mail){
    return filter_var($mail, FILTER_VALIDATE_EMAIL);
}

function verification_gestion_name(string $gestion_name):bool
{
    return ($gestion_name === "v_vetement" || $gestion_name === "v_boisson" || $gestion_name === "c_particulier" || $gestion_name === "c_pilate" || $gestion_name === "c_crosstrainning");

}