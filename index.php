<?php

define("URL", str_replace("index.php", "", (isset($_SERVER['HTTPS']) ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]"));

$method = $_SERVER['REQUEST_METHOD'];
// header('Content-Type: application/json; charset=UTF-8');

require "./models/user/user.manager.php";
require "./controllers/response.func.php";

$userManage = new UserManage;

try{
    $url = explode("/", filter_var($_REQUEST['page'], FILTER_SANITIZE_URL));
    if(!empty($url[0])){
        if($url[0] == "V1" && isset($url[1])){
            switch($method){
                case "GET":
                    // user----------------------------------------------------------------------------------------------
                    if($url[1] == "user-all")
                    {
                        echo Tools::return_json("méthode GET", $userManage->get_all_user());
                    }
                    // partner ------------------------------------------------------------------------------------------------
                    if($url[1] == "partner-all")
                    {
                        echo Tools::return_json("méthode GET", ['test' => "Liste partner + Droits"]);
                    }
                    if($url[1] == "partner" && isset($url[2]) && !isset($url[3]) && is_numeric($url[2])){
                        echo Tools::return_json("méthode GET", ['test' => "Partner avec pour id : ".$url[2]]);
                    }else{
                        if(isset($url[3])){
                            throw new Exception("Il y a trop d'argument");
                        }else{
                            throw new Exception("Il manque de(s) argument(s) ou argument érroné");
                        }
                    }
                    // structure --------------------------------------------------------------------------------------------------
                    if($url[1] == "structure-all")
                    {
                        echo Tools::return_json("méthode GET", ['test' => "Liste partner + Droits"]);
                    }
                break;
                case "POST":
                    echo Tools::return_json("méthode POST", ['test' => "POST"]);
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
