<?php

function cryptageMdp($pass){
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