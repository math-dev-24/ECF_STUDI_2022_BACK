<?php


header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST , PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

require "./core/Render.php";
require "./controllers/PartnerController.php";
require "./controllers/StructureController.php";
require "./core/Tools.php";
require "./controllers/UserController.php";

$partnerController = new PartnerController();
$structController = new StructureController();
$userController = new UserController();



if (!isset($_REQUEST['page'])){
    Render::sendJsonError("Bienvenue sur l'api");
    exit();
}else{
    $url = explode("/", filter_var($_REQUEST['page'], FILTER_SANITIZE_URL));
}

if ($url[0] !== "V1")
{
    Render::sendJsonError("Seul la version 1 est disponnible pour le moment");
    exit();
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
            $userPassword = Tools::hashMdp(Tools::dataSecure($data['password']));
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
            $structController->createStruct($userEmail, $structName, $structActive, $userName, $partnerId);
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
        if ($url[1] === "struct" && !isset($url[2])) {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $structId = Tools::dataSecure($data['struct_id']);
            $structName = Tools::dataSecure($data['struct_name']);
            $structController->updateStruct($structId, $structName);
        }
        if ($url[1] === "struct" && isset($url[2]) && $url[2] === "droit" && !isset($url[3])) {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $structId = Tools::dataSecure($data['struct_id']);
            $gestionName = Tools::dataSecure($data['gestion_name']);
            $gestionActif = Tools::dataSecure($data['gestion_active']);
            $structController->updateDroitStruct($structId, $gestionName, $gestionActif);
        }
        if ($url[1] === "struct" && isset($url[2]) && $url[2] === "active" && !isset($url[3])) {
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
            Render::sendJsonError("go Delete partner id :".$url[2]);
        }
        if ( isset($url[1]) && $url[1] === "struct" && isset($url[2]) && !isset($url[3]))
        {
            Render::sendJsonError("Go Delete struct id :".$url[2]);
        }
        if (isset($url[1]) && $url[1] === 'user' && isset($url[2]) && !isset($url[3]))
        {
            Render::sendJsonError("Go Delete User id :".$url[2]);
        }
        break;

}

