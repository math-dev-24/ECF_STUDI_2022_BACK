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

$apiController = new ApiController();


try{
    if(isset($_REQUEST['page']))
    {
        $url = explode("/", filter_var($_REQUEST['page'], FILTER_SANITIZE_URL));
    }else{
        $data = ["API" => "Version 1", "msg" => "Bienvenue sur l'api pour l'ecf 2022"];
        $apiController->sendJSON($data);
    }
    if(!empty($url[0])){
        if($url[0] === "V1" && !empty($url[1])){
            switch($method){
                case "GET":
                    // partner ------------------------------------------------------------------------------------------------
                    if($url[1] === $partner_url."-all" && !isset($url[2]))
                    {
                        $apiController->get_all_partner();
                    }
                    if($url[1] === $partner_url && isset($url[2]) && !isset($url[3]))
                    {
                        $id = data_secure($url[2]);
                        $apiController->get_partner_by_partnerId($id);
                    }
                    // structure --------------------------------------------------------------------------------------------------
                    if($url[1] === $structure_url."-all" && !isset($url[2]))
                    {
                        $apiController->get_all_struct();
                    }
                    if($url[1] === $structure_url && isset($url[2]))
                    {
                        $id = data_secure($url[2]);
                        $apiController->get_struct_by_structId($id);
                    }
                    // user ------------------------------------------------------------------------------------------------------
                if ($url[1] === "users" && !isset($url[2]))
                {
                    $apiController->get_all_user();
                }
                break;
                // REQUEST POST ---------------------------------------------------------------------------------------------------
                case "POST":
                    if($url[1] === "login" && !isset($url[2]))
                    {
                        $json = file_get_contents('php://input');
                        $data = json_decode($json, true);
                        $user_email =  $data['user_email'];
                        $user_password = hash_mdp(data_secure($data['user_password']));
                        $apiController->go_authentification($user_email, $user_password);
                    }
                    if($url[1] === "partner" && !isset($url[2]))
                    {
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $partner_name = data_secure($data['partner_name']);
                        $user_email = data_secure($data['user_email']);
                        $partner_active = data_secure($data['partner_active']);
                        $apiController->create_partner($partner_name, $user_email, $partner_active);
                    }
                    if($url[1] === "struct" && !isset($url[2]))
                    {
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $user_email = data_secure($data['user_email']);
                        $struct_name = data_secure($data['struct_name']);
                        $struct_active = data_secure($data['struct_active']);
                        $partner_id = data_secure($data['partner_id']);
                        $apiController->create_struct( $user_email, $struct_name, $struct_active, $partner_id);
                    }
                break;
                    //REQUEST PUT----------------------------------------------------------------------------------------------------
                case "PUT":
                    //partner _________________________________________________________________________________________________________
                    //Update partner
                    if($url[1] === $partner_url && !isset($url[2])){
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $partner_id = data_secure($data['partner_id']);
                        $partner_name = data_secure($data['partner_name']);
                        $logo_url = data_secure($data['logo_url']);
                        $apiController->update_partner($partner_id,$partner_name, $logo_url);
                    }
                    if ($url[1] === $partner_url && isset($url[2]) && $url[2] === "droit" && !isset($url[3])){
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $partner_id = data_secure($data['partner_id']);
                        $gestion_name = data_secure($data['gestion_name']);
                        $gestion_actif = data_secure($data['gestion_active']);
                        $apiController->update_droit_partner($partner_id, $gestion_name, $gestion_actif);
                    }
                    if ($url[1] === $partner_url && isset($url[2]) && $url[2] === "active" && !isset($url[3])){
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $partner_id = data_secure($data['partner_id']);
                        $partner_active = data_secure($data['partner_active']);
                        $apiController->update_active_partner($partner_id, $partner_active);
                    }
                    //struct _________________________________________________________________________________________________________
                    if($url[1] === $structure_url && !isset($url[2])){
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $struct_id = data_secure($data['struct_id']);
                        $struct_name = data_secure($data['struct_name']);
                        $apiController->update_struct($struct_id,$struct_name);
                    }
                    if ($url[1] === $structure_url && isset($url[2]) && $url[2] === "droit" && !isset($url[3])){
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $struct_id = data_secure($data['struct_id']);
                        $gestion_name = data_secure($data['gestion_name']);
                        $gestion_actif = data_secure($data['gestion_active']);
                        $apiController->update_droit_struct($struct_id, $gestion_name, $gestion_actif);
                    }
                    if ($url[1] === $structure_url && isset($url[2]) && $url[2] === "active" && !isset($url[3])){
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $struct_id = data_secure($data['struct_id']);
                        $struct_active = data_secure($data['struct_active']);
                        $apiController->update_active_struct($struct_id, $struct_active);
                    }
                    //User________________________________________________________________________________________________________________
                    if($url[1] === "user" && isset($url[2]) && !isset($url[3])){
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        $name_column = data_secure($url[2]);
                        $user_email = data_secure($data['user_email']);
                        $value_change = data_secure($data['value']);
                        $apiController->update_user($user_email, $name_column, $value_change);
                    }
                    break;
                case "DELETE":
                    if($url[1] === $partner_url && isset($url[2]) && !isset($url[3])){
                        $partner_id = data_secure($url[2]);
                        $apiController->delete_partner($partner_id);
                    }
                    if ($url[1] === $structure_url && isset($url[2]) && !isset($url[3])){
                        $struct_id = data_secure($url[2]);
                        $apiController->delete_struct($struct_id);
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
    $apiController->send_JSON_error($e);
}


