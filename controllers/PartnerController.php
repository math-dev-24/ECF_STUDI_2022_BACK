<?php

require_once "./models/PartnerManager.php";
require_once "./models/StructManager.php";
require_once "./models/UserManager.php";
require_once "./models/GestionManager.php";
require_once "./models/LogManager.php";

class PartnerController
{

    private PartnerManager $partnerManager;
    private StructManager $structManager;
    private UserManager $userManager;
    private GestionManager $gestionManager;
    private LogManager $logManager;

    public function __construct()
    {
        $this->partnerManager = new PartnerManager();
        $this->structManager = new StructManager();
        $this->userManager = new UserManager();
        $this->gestionManager = new GestionManager();
        $this->logManager = new LogManager();
    }

    /**
     * this function return all partner data
     * @return void
     */
    public  function getAllPartner(): void
    {
        Render::sendJSON($this->partnerManager->getAllPartner());
    }

    /**
     * this function return details data by partnerId
     * @param int $partnerId
     * @return void
     */
    public function getPartnerByPartnerId(int $partnerId): void
    {
        $partner = $this->partnerManager->getByPartnerId($partnerId);
        $struct = $this->structManager->getStructByPartnerId($partnerId);
        $data = [
            "partner_id" => $partnerId,
            "user_id" => $partner['user_id'],
            "user_name" => $partner['user_name'],
            "user_email" => $partner['email'],
            'profil_url' => $partner['profil_url'],
            "user_active" => $partner['user_active'],
            "partner_name" => $partner['partner_name'],
            "logo_url" => $partner['logo_url'],
            "partner_active" => $partner['partner_active'],
            "gestion" =>[
                "v_vetement" => $partner['v_vetement'],
                "v_boisson" => $partner['v_boisson'],
                "c_particulier" => $partner['c_particulier'],
                "c_crosstrainning" => $partner['c_crosstrainning'],
                "c_pilate" => $partner['c_pilate']
            ],
            "struct" => $struct
        ];
        Render::sendJSON($data);
    }

    /**
     * this function create Partner
     * @param string $partnerName
     * @param string $userEmail
     * @param int $partnerActive
     * @param string $userName
     * @return void
     */
    public function createPartner(string $partnerName, string $userEmail, int $partnerActive, string $userName):void
    {
        $passwordUser = Tools::hashMdp($partnerName);
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
            $gestionId = $this->gestionManager->createGestion();
            $partnerCreated = $this->partnerManager->createPartner($user['id'], $partnerName, $partnerActive, $gestionId);
            if ($partnerCreated){
                $partner = $this->partnerManager->getByUserId($user['id']);
                $this->logManager->addLog("Nouveau partenaire crée : ". $partner['partner_name']);
                Render::sendJSON($partner);
            }else{
                Render::sendJsonError("Erreur lors de la création du partenaire");
            }
        }
    }

    /**
     * this function update Partner
     * @param int $partnerId
     * @param string $partnerName
     * @param string $logoUrl
     * @return void
     */
    public function updatePartner(int $partnerId, string $partnerName, string $logoUrl):void
    {
        $partner = $this->partnerManager->getByPartnerId($partnerId);
        if ($partner['partner_name'] === $partnerName && $partner['logo_url'] === $logoUrl)
        {
            Render::sendJsonOK();
        }
        if ($this->partnerManager->updatePartner($partnerId, $partnerName, $logoUrl))
        {
            $this->getPartnerByPartnerId($partnerId);
        }else{
            Render::sendJsonError("Erreur lors de l'update");
        }
    }

    /**
     * this function update droit Partner
     * @param int $partnerId
     * @param string $gestionName
     * @param int $gestionActive
     * @return void
     */
    public function updateDroitPartner(int $partnerId, string $gestionName, int $gestionActive):void
    {
        if (!Tools::verificationGestionName($gestionName))
        {
            Render::sendJsonError("Nom du droit invalide");
        }
        $partner = $this->partnerManager->getGestionIdByPartnerId($partnerId);



        if ($this->gestionManager->updateGestionByDroitIdAndDroitName($partner['gestion_id'], $gestionName, $gestionActive))
        {
            if ($gestionActive === 0)
            {
                $structs = $this->structManager->getStructByPartnerId($partnerId);
                foreach ($structs as $struct){
                    if ($struct[$gestionName] === 1){
                        $this->gestionManager->updateGestionByDroitIdAndDroitName($struct['gestion_id'], $gestionName, 0);
                    }
                }
            }
            Render::sendJsonOK();
        }else{
            Render::sendJsonError("Erreur lors de l'update");
        }
    }

    /**
     * this function update active Partner
     * @param int $partnerId
     * @param int $partnerActive
     * @return void
     */
    public function updateActivePartner(int $partnerId, int $partnerActive):void
    {
        if ($this->partnerManager->updateActive($partnerId, $partnerActive))
        {
            if ($partnerActive === 0)
            {
                $structs = $this->structManager->getStructByPartnerId($partnerId);
                foreach ($structs as $struct){
                    if ($struct['struct_active'] === 1)
                    {
                        $this->structManager->updateActive($struct['id'],0);
                    }
                }
            }
            $partner = $this->partnerManager->getByPartnerId($partnerId);
            Render::sendJSON($partner);
        }else{
            Render::sendJsonError("Erreur lors de l'update");
        }
    }

    /**
     * this function delete Partner
     * @param int $partnerId
     * @return void
     */
    public function deletePartner(int $partnerId):void
    {
        $partner = $this->partnerManager->getByPartnerId($partnerId);
        $structs = $this->structManager->getStructByPartnerId($partnerId);

        //Supprimer les droits structures et structures
        foreach ($structs as $struct){
            $this->gestionManager->deleteByIdAndTableName($struct['gestion_id'],"gestion");
            $this->gestionManager->deleteByIdAndTableName($struct['id'], "struct");
        }
        //Supprimer droit partner / l'utilisateur et le partner à la fin
        $this->gestionManager->deleteByIdAndTableName($partner['gestion_id'], "gestion");
        $this->gestionManager->deleteByIdAndTableName($partner['user_id'], "user");
        $this->gestionManager->deleteByIdAndTableName($partnerId, "partner");
        Render::sendJsonOK();
    }
}