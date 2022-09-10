<?php

use JetBrains\PhpStorm\NoReturn;

require_once "./models/PartnerManager.php";
require_once "./models/StructManager.php";

class PartnerController
{

    private PartnerManager $partnerManager;
    private StructManager $structManager;

    public function __construct()
    {
        $this->partnerManager = new PartnerManager();
        $this->structManager = new StructManager();
    }

    #[NoReturn] public  function getAllPartner(): void
    {
        Render::send_JSON($this->partnerManager->getAllPartner());
        exit();
    }

    #[NoReturn] public function getPartnerByPartnerId(int $partnerId): void
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
        Render::send_JSON($data);
        exit();
    }
}