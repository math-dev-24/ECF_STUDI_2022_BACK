<?php


require_once "./models/PartnerManager.php";
require_once "./models/StructManager.php";
require_once "./models/UserManager.php";
require_once "./models/GestionManager.php";

class StructureController
{
    private PartnerManager $partnerManager;
    private StructManager $structManager;
    private UserManager $userManager;
    private GestionManager $gestionManager;

    public function __construct()
    {
        $this->partnerManager = new PartnerManager();
        $this->structManager = new StructManager();
        $this->userManager = new UserManager();
        $this->gestionManager = new GestionManager();
    }

    public function getAllStruct(): void
    {
        Render::sendJSON($this->structManager->getAllStruct());
    }

    public function getStructByStructId(int $structId):void
    {
        $struct = $this->structManager->getByStructId($structId);

        $data =[
            "struct_id" => $structId,
            "struct_name" => $struct['struct_name'],
            "struct_active" => $struct['struct_active'],
            "struct_adress" => $struct['struct_adress'],
            "struct_city" => $struct['struct_city'],
            "struct_postal" => $struct['struct_postal'],
            "partner_id" => $struct['partner_id'],
            "partner_user_id" => $struct['partner_user_id'],
            "partner_name" => $struct['partner_name'],
            "partner_active" => $struct['partner_active'],
            "user_id" => $struct['user_id'],
            "user_name" => $struct['user_name'],
            "user_email" => $struct['email'],
            "user_active" => $struct['user_active'],
            "gestion" =>[
                "v_vetement" => $struct['v_vetement'],
                "v_boisson" => $struct['v_boisson'],
                "c_particulier" => $struct['c_particulier'],
                "c_crosstrainning" => $struct['c_crosstrainning'],
                "c_pilate" => $struct['c_pilate']
            ]
        ];
        Render::sendJSON($data);
    }

    public function createStruct(string $userEmail,
                                 string $structName,
                                 int $structActive,
                                 string $userName,
                                 int $partnerId,
                                 string $structAdress,
                                 string $structCity,
                                 int $structPostal
                                ):void
    {
        $passwordUser = Tools::hashMdp($structName);
        if (!Tools::verificationEmail($userEmail))
        {
            Render::sendJsonError("Email invalide");
        }
        if (!$this->userManager->emailIsAvailable($userEmail))
        {
            Render::sendJsonError("Email non disponnible");
        }else{
            if (!$this->userManager->createUser($userEmail, $userName, $passwordUser)){
                Render::sendJsonError("Erreur lors de la création de l'utilisateur");
            }
            $user = $this->userManager->getUserByEmail($userEmail);
            $partner = $this->partnerManager->getByPartnerId($partnerId);
            $gestionId = $this->gestionManager->createGestionByPartner($partner);
            if ($gestionId){
                $structCreated = $this->structManager->createStruct($user['id'],$structName, $structActive, $gestionId, $partnerId, $structAdress, $structCity ,$structPostal);
                if ($structCreated){
                    $struct = $this->structManager->getByUserId($user['id']);
                    Render::sendJSON($struct);
                }else{
                    Render::sendJsonError("Erreur lors de la création du partenaire");
                }
            }
        }
    }

    public function updateStruct(int $structId, string $structName):void
    {
        $struct = $this->structManager->getByStructId($structId);
        if ($struct['struct_name'] === $structName)
        {
            Render::sendJsonOK();
        }
        if ($this->structManager->updateStruct($structId, $structName))
        {
            $struct_update = $this->structManager->getByStructId($structId);
            Render::sendJSON($struct_update);
        }else{
            Render::sendJsonError("Erreur lors de la modification");
        }
    }

    public function updateDroitStruct(int $structId, string $gestionName, int $gestionActive):void
    {
        if (!Tools::verificationGestionName($gestionName))
        {
            Render::sendJsonError("Nom du droit incorrect");
        }


        $struct = $this->structManager->getByStructId($structId);
        $partner = $this->partnerManager->getByPartnerId($struct['partner_id']);

        if ($partner[$gestionName] === 1)
        {
            $this->gestionManager->updateGestionByDroitIdAndDroitName($struct['gestion_id'],$gestionName, $gestionActive);
            Render::sendJsonOK();

        }else{
            Render::sendJsonError("Impossible de modifier.Le partner référent n'a pas l'autorisation !");
        }
    }

    public function updateActiveStruct(int $structId, int $structActive):void
    {
        $struct = $this->structManager->getByStructId($structId);
        $partner_lie = $this->partnerManager->getByPartnerId($struct['partner_id']);
        if ($partner_lie['partner_active'] === 0)
        {
            Render::sendJsonError("Partenaire référent non active.Impossible d'activé la structure");
        }
        if ($this->structManager->updateActive($structId, $structActive))
        {
            Render::sendJsonOK();
        }else{
            Render::sendJsonError("Erreur lors de l'update");
        }
    }

    public function deleteStruct(int $structId):void
    {
        $struct = $this->structManager->getByStructId($structId);
        $this->gestionManager->deleteByIdAndTableName($struct['gestion_id'], "gestion");
        $this->gestionManager->deleteByIdAndTableName($struct['user_id'],"user");
        $this->gestionManager->deleteByIdAndTableName($struct['struct_id'],"struct");
        Render::sendJsonOK();
    }
}