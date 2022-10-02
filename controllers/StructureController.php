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

    /**
     * this function return all struct
     * @return void
     */
    public function getAllStruct(): void
    {
        Render::sendJSON($this->structManager->getAllStruct());
    }

    /**
     * this function return details data by StructId
     * @param int $structId
     * @return void
     */
    public function getStructByStructId(int $structId):void
    {
        $struct = $this->structManager->getByStructId($structId);

        $data =[
            "struct_id" => $structId,
            "struct_name" => $struct['struct_name'],
            "struct_active" => $struct['struct_active'],
            "struct_address" => $struct['struct_address'],
            "struct_city" => $struct['struct_city'],
            "struct_postal" => $struct['struct_postal'],
            "partner_id" => $struct['partner_id'],
            "partner_user_id" => $struct['partner_user_id'],
            "partner_name" => $struct['partner_name'],
            "partner_active" => $struct['partner_active'],
            "user_id" => $struct['user_id'],
            "user_name" => $struct['user_name'],
            "user_email" => $struct['email'],
            'profil_url' => $struct['profil_url'],
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

    /**
     * this function create Struct
     * @param string $userEmail
     * @param string $structName
     * @param int $structActive
     * @param string $userName
     * @param int $partnerId
     * @param string $structaddress
     * @param string $structCity
     * @param int $structPostal
     * @return void
     */
    public function createStruct(string $userEmail,
                                 string $structName,
                                 int $structActive,
                                 string $userName,
                                 int $partnerId,
                                 string $structaddress,
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
                $structCreated = $this->structManager->createStruct($user['id'],$structName, $structActive, $gestionId, $partnerId, $structaddress, $structCity ,$structPostal);
                if ($structCreated){
                    $struct = $this->structManager->getByUserId($user['id']);
                    Render::sendJSON($struct);
                }else{
                    Render::sendJsonError("Erreur lors de la création du partenaire");
                }
            }
        }
    }

    /**
     * this function update Struct
     * @param int $structId
     * @param string $structName
     * @param string $structAddress
     * @param string $structCity
     * @param int $structPostal
     * @return void
     */
    public function updateStruct(int $structId, string $structName, string $structAddress, string $structCity, int $structPostal):void
    {
        $struct = $this->structManager->getByStructId($structId);
        if ($struct['struct_name'] === $structName &&
            $struct['struct_address'] === $structAddress &&
            $struct['struct_city'] === $structCity &&
            $struct['struct_postal'] === $structPostal
        )
        {
            Render::sendJsonOK();
        }

        if ($this->structManager->updateStruct($structId, $structName, $structAddress, $structCity, $structPostal))
        {
            $this->getStructByStructId($structId);
        }else{
            Render::sendJsonError("Erreur lors de la modification");
        }
    }

    /**
     * this function update Droit Struct
     * @param int $structId
     * @param string $gestionName
     * @param int $gestionActive
     * @return void
     */
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

    /**
     * this function update Active Struct
     * @param int $structId
     * @param int $structActive
     * @return void
     */
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

    /**
     * this function delete Struct
     * @param int $structId
     * @return void
     */
    public function deleteStruct(int $structId):void
    {
        $struct = $this->structManager->getByStructId($structId);
        $this->gestionManager->deleteByIdAndTableName($struct['gestion_id'], "gestion");
        $this->gestionManager->deleteByIdAndTableName($struct['user_id'],"user");
        $this->gestionManager->deleteByIdAndTableName($struct['struct_id'],"struct");
        Render::sendJsonOK();
    }
}