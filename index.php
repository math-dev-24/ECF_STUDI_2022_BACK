<?php

define("URL", str_replace("index.php", "", (isset($_SERVER['HTTPS']) ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]"));

// methode
$method = $_SERVER['REQUEST_METHOD'];
//préciser que l'on envoi du JSON
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
//import
require "./controllers/function.php";
require "./controllers/response.func.php";
require "./controllers/Api.controller.php";

$partner_url = "partner";
$structure_url = "struct";

const apiController = new ApiController();


try{
    if(isset($_REQUEST['page']))
    {
        $url = explode("/", filter_var($_REQUEST['page'], FILTER_SANITIZE_URL));
    }else{
        throw new Exception("Bienvenue sur l'api");
    }
    if(!empty($url[0])){
        if($url[0] === "V1" && !empty($url[1])){
            switch($method){
                case "GET":
                    // partner ------------------------------------------------------------------------------------------------
                    if($url[1] === $partner_url."-all" && !isset($url[2]))
                    {
                        apiController->get_all_partner();
                    }
                    if($url[1] === $partner_url && isset($url[2]))
                    {
                        $id = dataSecure($url[2]);
                        apiController->get_partner_by_partnerId($id);
                    }
                    // structure --------------------------------------------------------------------------------------------------
                    if($url[1] === $structure_url."-all"){apiController->get_all_struct();}
                    if($url[1] === $structure_url && isset($url[2]))
                    {
                        $id = dataSecure($url[2]);
                        apiController->get_struct_by_structId($id);
                    }
                break;
                // REQUEST POST ---------------------------------------------------------------------------------------------------
                case "POST":
                    if($url[1] === "login" && !isset($url[2]))
                    {
                        $email =  cryptageMdp(dataSecure($_POST['user_email']));
                        $password = dataSecure($_POST['user_password']);
                        apiController->go_authentification($email, $password);
                    }
                    if($url[1] === "partner" && !isset($url[2]))
                    {
                        $partner_name = dataSecure($_POST['partner_name']);
                        $user_email = dataSecure($_POST['user_email']);
                        $partner_active = dataSecure($_POST['partner_active']);
                        apiController->create_partner($partner_name, $user_email, $partner_active);
                    }
                    if($url[1] === "struct" && !isset($url[2]))
                    {
                        $user_email = dataSecure($_POST['user_email']);
                        $struct_name = dataSecure($_POST['struct_name']);
                        $struct_active = dataSecure($_POST['struct_active']);
                        $partner_id = dataSecure($_POST['partner_id']);
                        apiController->create_struct( $user_email, $struct_name, $struct_active, $partner_id);
                    }
                break;
                    //REQUEST PUT----------------------------------------------------------------------------------------------------
                case "PUT":
                    //partner _________________________________________________________________________________________________________
                    if($url[1] === $partner_url && isset($url[2]) && !isset($url[3])){
                        return "modification partner";
                    }
                    if ($url[1] === $partner_url && isset($url[2]) && $url[2] === "droit" && isset($url[3]) && !isset($url[4])){
                        return "modification droit partner";
                    }
                    if ($url[1] === $partner_url && isset($url[2]) && $url[2] === "active" && isset($url[3]) && !isset($url[4])){
                        return "modification droit partner";
                    }
                    //struct _________________________________________________________________________________________________________
                    if($url[1] === $structure_url && isset($url[2]) && !isset($url[3])){
                        return "modification struct";
                    }
                    if ($url[1] === $structure_url && isset($url[2]) && $url[2] === "droit" && isset($url[3]) && !isset($url[4])){
                        return "modification droit struct";
                    }
                    if ($url[1] === $structure_url && isset($url[2]) && $url[2] === "active" && isset($url[3]) && !isset($url[4])){
                        return "modification droit struct";
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
    apiController->sendJSON($e);
}


