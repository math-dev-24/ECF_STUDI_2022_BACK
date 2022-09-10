<?php

use JetBrains\PhpStorm\NoReturn;

require_once  "./models/StructManager.php";

class StructureController
{
    private StructManager $structManager;

    public function __construct()
    {
        $this->structManager = new StructManager();
    }

    #[NoReturn] public function getAllStruct(): void
    {
        Render::send_JSON($this->structManager->getAllStruct());
        exit();
    }

    #[NoReturn] public function getStructByStructId(int $structId):void
    {
        $struct = $this->structManager->get_by_structId($structId);

        $data =[
            "struct_id" => $structId,
            "struct_name" => $struct['struct_name'],
            "struct_active" => $struct['struct_active'],
            "partner_id" => $struct['partner_id'],
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
        Render::send_JSON($data);
        exit();
    }


}