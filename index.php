<?php


//header('Content-Type: application/json; charset=UTF-8');
//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Methods: GET, POST , PUT, DELETE");
//header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

require "./core/Render.php";
require "./controllers/PartnerController.php";
require "./controllers/StructureController.php";
require "./core/Tools.php";

$partnerController = new PartnerController();
$structController = new StructureController();



if (!isset($_REQUEST['page'])){
    Render::send_JSON_error("Bienvenue sur l'api");
    exit();
}else{
    $url = explode("/", filter_var($_REQUEST['page'], FILTER_SANITIZE_URL));
}

if ($url[0] !== "V1")
{
    Render::send_JSON_error("Seul la version 1 est disponnible pour le moment");
    exit();
}

//GESTION ROUTE ------------------------------------------------------------------------------------------

switch ($_SERVER['REQUEST_METHOD'])
{
    case "GET":
        //Partner-----------------------------------------------------------------------------------------
        if(isset($url[1]) && $url[1] === "partner")
        {
            if (!isset($url[2]))
            {
                $partnerController->getAllPartner();
            }else{
                if (!isset($url[3])){
                    $partnerId = Tools::data_secure($url[2]);
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
                    $structId = Tools::data_secure($url[2]);
                    $structController->getStructByStructId($structId);
                }
            }
        }
        Render::send_JSON_error("No route match");
        break;
    case "POST":
        if (isset($url[1]) && $url[1] === "login")
        {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $userEmail = Tools::data_secure($data['user_email']);
            $userPassword = Tools::hash_mdp(Tools::data_secure($data['password']));

        }

        Render::send_JSON_error("No route match");
        break;
}


