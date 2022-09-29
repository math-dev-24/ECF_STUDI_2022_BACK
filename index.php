<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT,DELETE");
header('Content-Type: application/json');


require "./core/Render.php";
require "./controllers/PartnerController.php";
require "./controllers/StructureController.php";
require "./controllers/UserController.php";
require "./core/Tools.php";
require "./Auth.php";
require "./core/JWT.php";

$partnerController = new PartnerController();
$structController = new StructureController();
$userController = new UserController();
$auth = new Auth();
$JWT = new JWT();


//Vérification URL Vide
if (!isset($_REQUEST['page'])){
    Render::sendJsonError("Bienvenue sur l'api");
}

$url = explode("/", filter_var($_REQUEST['page'], FILTER_SANITIZE_URL));

if ($url[0] !== "V1")
{
    Render::sendJsonError("Seul la version 1 est disponnible pour le moment");
}

$headerJwt = [
    "alg"=> "HS256",
  "typ" => "JWT"
];
$payloadJwt = [
  'user' => "Yes",
    'grade' => "admin"
];



//gestion TOKEN ------------------------------------------------------------------------------------------

if ($auth->getToken())
{
    $token = $auth->getToken();
    if (!$JWT->isValid($token)){
        http_response_code(400);
        Render::sendJsonError("Token invalide");
    }
    if ($JWT->isExpired($token)){
        http_response_code(403);
        Render::sendJsonError("Token expiré");
    }
    if (!$JWT->check($token)){
        http_response_code(403);
        Render::sendJsonError("Le token est invalide");
    }

    Render::sendJsonError($JWT->getPayload($token));

}else{
    http_response_code(400);
    Render::sendJsonError("Token introuvable");
}






//GESTION ROUTE ------------------------------------------------------------------------------------------

switch ($_SERVER['REQUEST_METHOD'])
{
    //GET -------------------------------------------------------------------------------------------------------
    case "GET":
        //Partner-----------------------------------------------------------------------------------------
        if(isset($url[1]) && $url[1] === "partner")
        {
            if (!isset($url[2]))
            {
                $partnerController->getAllPartner();
            }else{
                if (!isset($url[3])){
                    $partnerId = Tools::dataSecure($url[2]);
                    $partnerController->getPartnerByPartnerId($partnerId);
                }
            }
        }
        //Structure----------------------------------------------------------------------------------------
        if (isset($url[1]) && $url[1] == "struct")
        {
            if (!isset($url[2]))
            {
                $structController->getAllStruct();
            }else{
                if (!isset($url[3])){
                    $structId = Tools::dataSecure($url[2]);
                    $structController->getStructByStructId($structId);
                }
            }
        }
        if (isset($url[1]) && $url[1] == "user" && !isset($url[2]))
        {
            $userController->getAllUser();
        }
        Render::sendJsonError("No route match");
        break;
        //POST -------------------------------------------------------------------------------------------------------
    case "POST":
        if (isset($url[1]) && $url[1] === "login" && !isset($url[2]))
        {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $userEmail = Tools::dataSecure($data['user_email']);
            $userPassword = Tools::hashMdp(Tools::dataSecure($data['user_password']));
            $userController->goConnect($userEmail, $userPassword);
        }
        if (isset($url[1]) && $url[1] === "partner" && !isset($url[2]))
        {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $partnerName = Tools::dataSecure($data['partner_name']);
            $userMail = Tools::dataSecure($data['user_email']);
            $userName = Tools::dataSecure($data['user_name']);
            $partnerActive = Tools::dataSecure($data['partner_active']);
            $partnerController->createPartner($partnerName, $userMail, $partnerActive, $userName);
        }
        if (isset($url[1]) && $url[1] === "struct" && !isset($url[2]))
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
        //PARTNER -----------------------------------------------------------------------------------------------
        if ( isset($url[1]) && $url[1] == "partner" && !isset($url[2]))
        {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $partnerId = Tools::dataSecure($data['partner_id']);
            $partnerName = Tools::dataSecure($data['partner_name']);
            $logoUrl = Tools::dataSecure($data['logo_url']);
            $partnerController->updatePartner($partnerId, $partnerName, $logoUrl);
        }

        if ( isset($url[1]) && $url[1] === "partner" && isset($url[2]) && $url[2] === "droit" && !isset($url[3]))
        {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $partnerId = Tools::dataSecure($data['partner_id']);
            $gestionName = Tools::dataSecure($data['gestion_name']);
            $gestionActif = Tools::dataSecure($data['gestion_active']);
            $partnerController->updateDroitPartner($partnerId, $gestionName, $gestionActif);
        }

        if ( isset($url[1]) && $url[1] === "partner" && isset($url[2]) && $url[2] === "active" && !isset($url[3]))
        {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $partnerId = Tools::dataSecure($data['partner_id']);
            $partnerActive = Tools::dataSecure($data['partner_active']);
            $partnerController->updateActivePartner($partnerId, $partnerActive);
        }
        //STRUCT--------------------------------------------------------------------------------------------------
        if (isset($url[1]) && $url[1] === "struct" && !isset($url[2])) {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $structId = Tools::dataSecure($data['struct_id']);
            $structName = Tools::dataSecure($data['struct_name']);
            $structAddress = Tools::dataSecure($data['struct_address']);
            $structCity = Tools::dataSecure($data['struct_city']);
            $structPostal = Tools::dataSecure($data['struct_postal']);
            $structController->updateStruct($structId, $structName, $structAddress, $structCity, $structPostal);
        }
        if (isset($url[1]) && $url[1] === "struct" && isset($url[2]) && $url[2] === "droit" && !isset($url[3])) {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $structId = Tools::dataSecure($data['struct_id']);
            $gestionName = Tools::dataSecure($data['gestion_name']);
            $gestionActif = Tools::dataSecure($data['gestion_active']);
            $structController->updateDroitStruct($structId, $gestionName, $gestionActif);
        }
        if (isset($url[1]) && $url[1] === "struct" && isset($url[2]) && $url[2] === "active" && !isset($url[3])) {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $structId = Tools::dataSecure($data['struct_id']);
            $structActive = Tools::dataSecure($data['struct_active']);
            $structController->updateActiveStruct($structId, $structActive);
        }
        //USER---------------------------------------------------------------------------------------------------
        if ( isset($url[1]) && $url[1] === "user" && isset($url[2]) && !isset($url[3])) {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $nameColumn = Tools::dataSecure($url[2]);
            $userEmail = Tools::dataSecure($data['user_email']);
            $valueChange = Tools::dataSecure($data['value']);
            $userController->updateUser($userEmail, $nameColumn, $valueChange);
        }
        break;
        //DELETE--------------------------------------------------------------------------------------------------
    case "DELETE":
        if( isset($url[1]) && $url[1] === "partner" && isset($url[2]) && !isset($url[3]))
        {
            $partnerId = Tools::dataSecure($url[2]);
            $partnerController->deletePartner($partnerId);
        }
        if ( isset($url[1]) && $url[1] === "struct" && isset($url[2]) && !isset($url[3]))
        {
            $structId = Tools::dataSecure($url[2]);
            $structController->deleteStruct($structId);
        }
        break;
    default:
        http_response_code(405);
        Render::sendJsonError("No route match");

}

