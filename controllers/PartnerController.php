<?php

require_once "./models/PartnerManager.php";
require_once "./models/StructManager.php";
require_once "./models/UserManager.php";
require_once "./models/GestionManager.php";

class PartnerController
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

    public  function getAllPartner(): void
    {
        Render::sendJSON($this->partnerManager->getAllPartner());
    }

    public function getPartnerByPartnerId(int $partnerId): void
    {
        $partner = $this->partnerManager->getByPartnerId($partnerId);
        $struct = $this->structManager->getStructByPartnerId($partnerId);
        $data = [
            "partner_id" => $partnerId,
            "user_id" => $partner['user_id'],
            "user_name" => $partner['user_name'],
            "user_email" => $partner['email'],
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
            $partner = $this->partnerManager->createPartner($user['id'], $partnerName, $partnerActive, $gestionId);
            if ($partner){
                Render::sendJsonOK();
            }else{
                Render::sendJsonError("Erreur lors de la création du partenaire");
            }
        }
    }

    public function updatePartner(int $partnerId, string $partnerName, string $logoUrl):void
    {
        $partner = $this->partnerManager->getByPartnerId($partnerId);
        if ($partner['partner_name'] === $partnerName && $partner['logo_url'] === $logoUrl)
        {
            Render::sendJsonOK();
        }
        if ($this->partnerManager->updatePartner($partnerId, $partnerName, $logoUrl))
        {
            Render::sendJsonOK();
        }else{
            Render::sendJsonError("Erreur lors de l'update");
        }
    }

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
            Render::sendJsonOK();
        }else{
            Render::sendJsonError("Erreur lors de l'update");
        }
    }
}