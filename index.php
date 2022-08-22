<?php

define("URL", str_replace("index.php", "", (isset($_SERVER['HTTPS']) ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]"));

// methode
$method = $_SERVER['REQUEST_METHOD'];
//préciser que l'on envoi du JSON
header('Content-Type: application/json; charset=UTF-8');
//import
require "./controllers/function.php";
require "./controllers/response.func.php";
require "./controllers/Api.controller.php";

$partner_url = "partner";
$structure_url = "structure";

const apiController = new ApiController();


try{
    $url = explode("/", filter_var($_REQUEST['page'], FILTER_SANITIZE_URL));
    if(!empty($url[0])){
        if($url[0] === "V1" && !empty($url[1])){
            switch($method){
                case "GET":
                    // partner ------------------------------------------------------------------------------------------------
                    if($url[1] === $partner_url."-all" && !isset($url[2])){apiController->get_All_partner();}




                    if($url[1] === $partner_url && isset($url[2]) && !isset($url[3]) && is_numeric($url[2]))
                    {
                        echo Tools::return_json("méthode GET", ['test' => "Partner avec droit pour id : ".$url[2]]);
                    }else{
                        if($url[1] === $partner_url)
                        {
                            isset($url[3]) ? Tools::msg_argument(Tools::TROP_LONG) : "";
                            !is_numeric($url[2]) ? Tools::msg_argument(Tools::NBR_ARG) : "";
                        }
                    }

                    // structure --------------------------------------------------------------------------------------------------
                    if($url[1] === $structure_url."-all")
                    {
                        echo Tools::return_json("méthode GET", ['test' => "Liste partner + Droits"]);
                    }
                    if($url[1] === $structure_url && isset($url[2]) && !isset($url[3]) && is_numeric($url[2]))
                    {
                        echo Tools::return_json("méthode GET", ['test' => "Structure avec droit pour id : ".$url[2]]);
                    }else{
                        if($url[1] === $structure_url)
                        {
                            isset($url[3]) ? Tools::msg_argument(Tools::TROP_LONG) : "";
                        }
                    }
                break;
                case "POST":
                    if($url[1] === "login"){
                        $email =  cryptageMdp(dataSecure($_POST['email']));
                        $password = dataSecure($_POST['password']);

                    }
                break;
            }
        }else{
            throw new Exception("Erreur");
        }
    }else{
        throw new Exception("Erreur");
    }


}
catch(Exception $e){
    echo Tools::return_json("Erreur", ["message" => $e->getMessage()]);
}


