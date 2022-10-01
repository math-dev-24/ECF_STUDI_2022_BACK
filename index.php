<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET,POST,PUT,PATCH,DELETE');
header('Access-Control-Allow-Headers: Content-Type,Access-Control-Allow-Headers,Authorization,X-Requested-With');

define("URL" , explode("/", filter_var($_REQUEST['page'], FILTER_SANITIZE_URL)));
define("METHOD" , $_SERVER['REQUEST_METHOD']);

if (METHOD == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

require_once "./controllers/PartnerController.php";
require_once "./controllers/StructureController.php";
require_once "./controllers/UserController.php";
require_once "./core/JWT.php";
require_once "./core/Auth.php";
require "./core/Tools.php";
require "./core/Render.php";

$JWT = new JWT();
$Auth = new Auth();
$partnerController = new PartnerController();
$structController = new StructureController();
$userController = new UserController();


if (METHOD === "POST" && isset(URL[1]) && URL[1] === "login" && !isset(URL[2])){
    if ($Auth->verifToken()){
        $userController->goConnectWithToken($Auth->getToken());
    }else{
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        $userEmail = Tools::dataSecure($data['user_email']);
        $userPassword = Tools::hashMdp(Tools::dataSecure($data['user_password']));
        $userController->goConnect($userEmail, $userPassword);
    }
}
else{

    if (!$Auth->verifToken()){
        http_response_code(400);
        Render::sendJsonError("Token Inexistant|invalide|Expiré");
    }else{
        $payload = $JWT->getPayload($Auth->getToken());
        $adminToken = $payload['is_admin'] === 1;
        $emailToken = $payload['email'];
        $nameToken = $payload['user_name'];
    }

// GESTION ROUTE ------------------------------------------------------------------------------------------
    switch (METHOD)
    {
        //GET -------------------------------------------------------------------------------------------------------
        case "GET":
            //Partner-----------------------------------------------------------------------------------------
            if(isset(URL[1]) && URL[1] === "partner")
            {
                if (!isset(URL[2]))
                {
                    $partnerController->getAllPartner();
                }else{
                    if (!isset(URL[3])){
                        $partnerId = Tools::dataSecure(URL[2]);
                        $partnerController->getPartnerByPartnerId($partnerId);
                    }
                }
            }
            //Structure----------------------------------------------------------------------------------------
            if (isset(URL[1]) && URL[1] == "struct")
            {
                if (!isset(URL[2]))
                {
                    $structController->getAllStruct();
                }else{
                    if (!isset(URL[3])){
                        $structId = Tools::dataSecure(URL[2]);
                        $structController->getStructByStructId($structId);
                    }
                }
            }
            Render::sendJsonError("No route match");
            break;
        //POST -------------------------------------------------------------------------------------------------------
        case "POST":
            if (isset(URL[1]) && URL[1] === "partner" && !isset(URL[2]) && $adminToken)
            {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                $partnerName = Tools::dataSecure($data['partner_name']);
                $userMail = Tools::dataSecure($data['user_email']);
                $userName = Tools::dataSecure($data['user_name']);
                $partnerActive = Tools::dataSecure($data['partner_active']);
                $partnerController->createPartner($partnerName, $userMail, $partnerActive, $userName);
            }

            if (isset(URL[1]) && URL[1] === "struct" && !isset(URL[2]) && $adminToken)
            {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                $userEmail = Tools::dataSecure($data['user_email']);
                $structName = Tools::dataSecure($data['struct_name']);
                $structActive = Tools::dataSecure($data['struct_active']);
                $partnerId = Tools::dataSecure($data['partner_id']);
                $userName = Tools::dataSecure($data['user_name']);
                $structAddress = Tools::dataSecure($data['struct_address']);
                $structCity = Tools::dataSecure($data['struct_city']);
                $structPostal = Tools::dataSecure($data['struct_postal']);
                $structController->createStruct($userEmail, $structName, $structActive, $userName, $partnerId, $structAddress, $structCity, $structPostal);
            }
            Render::sendJsonError("No route match");
            break;
        //PUT -------------------------------------------------------------------------------------------------
        case "PUT":
            // PARTNER -----------------------------------------------------------------------------------------------
            if ( isset(URL[1]) && URL[1] == "partner" && !isset(URL[2]) && $adminToken)
            {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                $partnerId = Tools::dataSecure($data['partner_id']);
                $partnerName = Tools::dataSecure($data['partner_name']);
                $logoUrl = Tools::dataSecure($data['logo_url']);
                $partnerController->updatePartner($partnerId, $partnerName, $logoUrl);
            }

            if ( isset(URL[1]) && URL[1] === "partner" && isset(URL[2]) && URL[2] === "droit" && !isset(URL[3]) && $adminToken)
            {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                $partnerId = Tools::dataSecure($data['partner_id']);
                $gestionName = Tools::dataSecure($data['gestion_name']);
                $gestionActif = Tools::dataSecure($data['gestion_active']);
                $partnerController->updateDroitPartner($partnerId, $gestionName, $gestionActif);
            }

            if ( isset(URL[1]) && URL[1] === "partner" && isset(URL[2]) && URL[2] === "active" && !isset(URL[3]) && $adminToken)
            {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                $partnerId = Tools::dataSecure($data['partner_id']);
                $partnerActive = Tools::dataSecure($data['partner_active']);
                $partnerController->updateActivePartner($partnerId, $partnerActive);
            }
            // STRUCT--------------------------------------------------------------------------------------------------
            if (isset(URL[1]) && URL[1] === "struct" && !isset(URL[2]) && $adminToken)
            {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                $structId = Tools::dataSecure($data['struct_id']);
                $structName = Tools::dataSecure($data['struct_name']);
                $structAddress = Tools::dataSecure($data['struct_address']);
                $structCity = Tools::dataSecure($data['struct_city']);
                $structPostal = Tools::dataSecure($data['struct_postal']);
                $structController->updateStruct($structId, $structName, $structAddress, $structCity, $structPostal);
            }
            if (isset(URL[1]) && URL[1] === "struct" && isset(URL[2]) && URL[2] === "droit" && !isset(URL[3]) && $adminToken)
            {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                $structId = Tools::dataSecure($data['struct_id']);
                $gestionName = Tools::dataSecure($data['gestion_name']);
                $gestionActif = Tools::dataSecure($data['gestion_active']);
                $structController->updateDroitStruct($structId, $gestionName, $gestionActif);
            }
            if (isset(URL[1]) && URL[1] === "struct" && isset(URL[2]) && URL[2] === "active" && !isset(URL[3]) && $adminToken)
            {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                $structId = Tools::dataSecure($data['struct_id']);
                $structActive = Tools::dataSecure($data['struct_active']);
                $structController->updateActiveStruct($structId, $structActive);
            }
            //USER---------------------------------------------------------------------------------------------------
            if ( isset(URL[1]) && URL[1] === "user" && isset(URL[2]) && !isset(URL[3]))
            {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                $nameColumn = Tools::dataSecure(URL[2]);
                $userEmail = Tools::dataSecure($data['user_email']);
                $valueChange = Tools::dataSecure($data['value']);
                if ($adminToken || $userEmail === $emailToken){
                    $userController->updateUser($userEmail, $nameColumn, $valueChange);
                }else{
                    Render::sendJsonError("Impossible d'update");
                }
            }
            //---------------------------------------------------------------------------------------------------------------
            Render::sendJsonError("No route match");
            break;
        //DELETE--------------------------------------------------------------------------------------------------
        case "DELETE":
            if( isset(URL[1]) && URL[1] === "partner" && isset(URL[2]) && !isset(URL[3]) && $adminToken)
            {
                $partnerId = Tools::dataSecure(URL[2]);
                $partnerController->deletePartner($partnerId);
            }
            if ( isset(URL[1]) && URL[1] === "struct" && isset(URL[2]) && !isset(URL[3]) && $adminToken)
            {
                $structId = Tools::dataSecure(URL[2]);
                $structController->deleteStruct($structId);
            }
            //---------------------------------------------------------------------------------------------------------------
            Render::sendJsonError("No route match");
            break;
        default:
            Render::sendJsonError("No route match");

    }
}

