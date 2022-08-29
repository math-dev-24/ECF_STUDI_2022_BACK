<?php

define("URL", str_replace("index.php", "", (isset($_SERVER['HTTPS']) ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]"));

// methode
$method = $_SERVER['REQUEST_METHOD'];
//préciser que l'on envoi du JSON
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST , PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

//import
require "./controllers/function.php";
require "./controllers/Api.controller.php";

$partner_url = "partner";
$structure_url = "struct";

const apiController = new ApiController();


try{
    if(isset($_REQUEST['page']))
    {
        $url = explode("/", filter_var($_REQUEST['page'], FILTER_SANITIZE_URL));
    }else{
        $data = ["API" => "Version 1", "msg" => "Bienvenue sur l'api pour l'ecf 2022"];
        apiController->sendJSON($data);
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
                    if($url[1] === $partner_url && isset($url[2]) && !isset($url[3]))
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
                        $json = file_get_contents('php://input');
                        $data = json_decode($json, true);
                        $user_email =  $data['user_email'];
                        $user_password = cryptageMdp(dataSecure($data['user_password']));
                        apiController->go_authentification($user_email, $user_password);
                    }
                    if($url[1] === "partner" && !isset($url[2]))
                    {
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $partner_name = dataSecure($data['partner_name']);
                        $user_email = dataSecure($data['user_email']);
                        $partner_active = dataSecure($data['partner_active']);
                        apiController->create_partner($partner_name, $user_email, $partner_active);
                    }
                    if($url[1] === "struct" && !isset($url[2]))
                    {
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $user_email = dataSecure($data['user_email']);
                        $struct_name = dataSecure($data['struct_name']);
                        $struct_active = dataSecure($data['struct_active']);
                        $partner_id = dataSecure($data['partner_id']);
                        apiController->create_struct( $user_email, $struct_name, $struct_active, $partner_id);
                    }
                break;
                    //REQUEST PUT----------------------------------------------------------------------------------------------------
                case "PUT":
                    //partner _________________________________________________________________________________________________________
                    if($url[1] === $partner_url && !isset($url[2])){
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $partner_id = dataSecure($data['partner_id']);
                        $partner_name = dataSecure($data['partner_name']);
                        $partner_active = dataSecure($data['partner_active']);
                        $logo_url = dataSecure($data['logo_url']);
                        apiController->update_partner($partner_id,$partner_name, $partner_active, $logo_url);
                    }
                    if ($url[1] === $partner_url && isset($url[2]) && $url[2] === "droit" && !isset($url[3])){
                        return "modification droit partner";
                    }
                    if ($url[1] === $partner_url && isset($url[2]) && $url[2] === "active" && !isset($url[3])){
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $partner_id = dataSecure($data['partner_id']);
                        $partner_active = dataSecure($data['partner_active']);
                        apiController->update_active_partner($partner_id, $partner_active);
                    }
                    //struct _________________________________________________________________________________________________________
                    if($url[1] === $structure_url && !isset($url[2])){
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $struct_id = dataSecure($data['struct_id']);
                        $struct_name = dataSecure($data['struct_name']);
                        $struct_active = dataSecure($data['struct_active']);
                        apiController->update_struct($struct_id,$struct_name, $struct_active);
                    }
                    if ($url[1] === $structure_url && isset($url[2]) && $url[2] === "droit" && !isset($url[3])){
                        return "modification droit struct";
                    }
                    if ($url[1] === $structure_url && isset($url[2]) && $url[2] === "active" && !isset($url[3])){
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $struct_id = dataSecure($data['struct_id']);
                        $struct_active = dataSecure($data['struct_active']);
                        apiController->update_active_struct($struct_id, $struct_active);
                    }
                    break;
                case "DELETE":
                    if($url[1] === $partner_url && isset($url[2]) && !isset($url[3])){
                        $partner_id = dataSecure($url[2]);
                        apiController->delete_partner($partner_id);
                    }
                    if ($url[1] === $structure_url && isset($url[2]) && !isset($url[3])){
                        $struct_id = dataSecure($url[2]);
                        apiController->delete_struct($struct_id);
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


