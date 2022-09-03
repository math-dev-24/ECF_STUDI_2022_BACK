<?php

require_once "./models/partner.manager.php";
require_once "./models/user.manager.php";
require_once "./models/struct.manager.php";
require_once "./models/gestion.manager.php";
require_once "function.php";

class ApiController{
    private PartnerManager $partnerManager;
    private UserManager $userManager;
    private StructManager $structManager;
    private GestionManager $gestionManager;

    public function __construct()
    {
        $this->partnerManager = new PartnerManager;
        $this->userManager = new UserManager;
        $this->structManager = new StructManager;
        $this->gestionManager = new GestionManager;
    }

//Render --------------------------------------------------------------------------------------------------------------------------------
    public function send_JSON($infos): void
    {
        echo json_encode($infos, JSON_UNESCAPED_UNICODE);
    }
    public function send_JSON_error($info):void
    {
        $data = ["error" => $info];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    public function send_JSON_OK():void
    {
        echo json_encode(['ok'=>'ok'], JSON_UNESCAPED_UNICODE);
    }

//Partner --------------------------------------------------------------------------------------------------------------------------------
    public  function get_all_partner(): void
    {
        $this->send_JSON($this->partnerManager->get_all_partner());
    }
    public function get_partner_by_partnerId(int $partnerId):void
    {
        $partner = $this->partnerManager->get_by_partnerId($partnerId);
        $user = $this->userManager->get_user_by_id($partner['user_id']);
        $struct = $this->structManager->get_by_partnerId($partner['id']);
        $this->send_JSON([
            "partner_id" => $partnerId,
            "user_id" => $partner['user_id'],
            "user_name" => $user['user_name'],
            "user_email" => $user['email'],
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
        ]);
    }
    public function create_partner(string $partner_name, string $user_email, int $partner_active):void
    {
        $password =hash_mdp($partner_name);
        if (verification_mail($user_email)){
            $this->send_JSON_error("Email non valide");
            exit();
        }
        if(!$this->userManager->email_is_available($user_email)){
            $this->send_JSON_error("Email non disponible");
        }else{
            if ($this->userManager->create_user($user_email, $password)){
                $user = $this->userManager->get_user_by_email($user_email);
                $gestion_id = $this->gestionManager->create_gestion();
                $partner = $this->partnerManager->create_partner($user['id'],$partner_name, $partner_active, $gestion_id['id']);
                if ($partner){
                    $this->send_JSON_OK();
                }else{
                    $this->send_JSON_error("Erreur lors de la création du partenaire");
                }
                //Tools::sendMail($user_email,"inscription" ,"Bonjour, Vous êtes maintenant inscrit en temps que partenaire. Voici votre mot de passe : ".$user['password'].". Il est à changer dès la première connexion. Merci Bonne journée");

                exit();
            }else{
                $this->send_JSON_error("Erreur lors de la création de l'utilisateur");
            }
        }
    }
    public function update_active_partner(int $partner_id, int $partner_active):void
    {
        if($this->partnerManager->update_active($partner_id, $partner_active)){
            $this->get_partner_by_partnerId($partner_id);
        }else{
            $this->send_JSON_error("Erreur lors de la création");
        }
    }
    public function update_droit_partner(int $partner_id, string $gestion_name, int $gestion_active):void
    {
        if (!verification_gestion_name($gestion_name)){
            $this->send_JSON_error("gestion name invalide");
        }else{
            $partner = $this->partnerManager->get_gestionId_by_partnerId($partner_id);
            if($this->gestionManager->update_gestion_by_droitname_droitid($partner['gestion_id'],$gestion_name, $gestion_active))
            {
                if ($gestion_active === 0){
                    $structure = $this->structManager->get_by_partnerId($partner_id);
                    $data = [];
                    foreach ($structure as $struct){
                        if ($struct[$gestion_name] === 1){
                            $this->gestionManager->update_gestion_by_droitname_droitid($struct['gestion_id'],$gestion_name, 0);
                        }
                    }
                }
                $this->get_partner_by_partnerId($partner_id);

            }else{
                $this->send_JSON_error("erreur lors de l'update");
            }
        }
    }
    public function update_partner(int $partner_id,string $partner_name, int $partner_active, int $logo_url):void
    {
        if($this->partnerManager->update_partner($partner_id,$partner_name,$partner_active,$logo_url)){
            $this->get_partner_by_partnerId($partner_id);
        }else{
            $this->send_JSON_error("Erreur lors de la mise à jours");
        }
    }
    public function delete_partner(int $partner_id):void
    {
        $this->send_JSON_error(["error"=> "demande suppresion struct".$partner_id.". Route afaire"]);
    }
//struct -----------------------------------------------------------------------------------------------------------------
    public function get_all_struct(): void
    {
        $this->send_JSON($this->structManager->get_all_struct());
    }
    public function get_struct_by_structId(int $structId):void
    {
        $struct = $this->structManager->get_by_structId($structId);
        $user = $this->userManager->get_user_by_id($struct['user_id']);
        $partner = $this->partnerManager->get_by_partnerId($struct['partner_id']);
        $this->send_JSON([
            "struct_id" => $structId,
            "partner_id" => $partner['id'],
            "partner_name" => $partner['partner_name'],
            "user_id" => $struct['user_id'],
            "user_name" => $user['user_name'],
            "user_email" => $user['email'],
            "struct_name" => $struct['struct_name'],
            "struct_active" => $struct['struct_active'],
            "gestion" =>[
                "v_vetement" => $struct['v_vetement'],
                "v_boisson" => $struct['v_boisson'],
                "c_particulier" => $struct['c_particulier'],
                "c_crosstrainning" => $struct['c_crosstrainning'],
                "c_pilate" => $struct['c_pilate']
            ]
        ]);
    }

    public function update_droit_struct(int $struct_id, string $gestion_name, int $gestion_active):void
    {
        if (verification_gestion_name($gestion_name)){
            $structure = $this->structManager->get_by_structId($struct_id);
            $partner = $this->partnerManager->get_by_partnerId($structure['partner_id']);
            if ($partner[$gestion_name] === 1){
                $this->gestionManager->update_gestion_by_droitname_droitid($structure['gestion_id'],$gestion_name, $gestion_active);
                $this->send_JSON_OK();
            }else{
                apiController->send_JSON_error("Impossible de modifier le partner référent n'a pas l'autorisation");
            }
        }else{
            apiController->send_JSON_error("Erreur du nom de la gestion modifié");
        }
    }

    public function update_active_struct(int $struct_id, int $struct_active):void
    {
        if($this->structManager->update_active($struct_id,$struct_active)){
            $this->send_JSON(["ok" => "ok"]);
        }else{
            $this->send_JSON_error("Erreur lors de la création");
        }
    }
    public function update_struct(int $struct_id, string $struct_name, string $struct_active):void
    {
        if ($this->structManager->update_struct($struct_id,$struct_name,$struct_active)){
            $this->send_JSON_error(['ok' => "ok"]);
        }else{
            $this->send_JSON_error("Erreur lors de la modification");
        }
    }
    public function delete_struct(int $struct_id):void
    {
        $this->send_JSON(["msg"=> "demande suppresion struct".$struct_id]);
    }

    public function create_struct(string $user_email, string $struct_name, int $struct_active, int $partner_id):void
    {
        $password = hash_mdp($struct_name);
        if (verification_mail($user_email)){
            $this->send_JSON_error("Email invalide");
            exit();
        }
        if (!$this->userManager->email_is_available($user_email)){
            $this->send_JSON_error("Email non disponible");
        }else{
            if(!$this->userManager->create_user($user_email,$password)){
                $this->send_JSON_error("Erreurs lors de la création du compte");
            }else{
                $user = $this->userManager->get_user_by_email($user_email);
                $partner = $this->partnerManager->get_by_partnerId($partner_id);
                $gestion_id = $this->gestionManager->create_gestion_by_partner($partner);
                if($gestion_id){
                    $struct = $this->structManager->create_struct($user["id"], $struct_name, $struct_active, $gestion_id['id'],$partner_id);

                    if ($struct){
                        $this->send_JSON_OK();
                    }else{
                        $this->send_JSON_error("Erreur lors de la création");
                    }
                    //Tools::sendMail($user_email, "inscription","Bonjour, Vous êtes maintenant inscrit en temps que structure. Voici votre mot de passe : ".$struct_name.". Il est à changer dès la première connexion. Merci Bonne journée");

                }else{
                    $this->send_JSON_error("Erreur lors de la création de gestion");
                }
            }
        }
    }

//user ---------------------------------------------------------------------------------------------------------------------------------

    public function go_authentification(string $email, string $password): void
    {
        $user = $this->userManager->get_user_by_email($email);
        if($user['password'] === $password){
            $this->send_JSON([
                "id" => $user['id'],
                "email" => $user["email"],
                "first_connect" => $user['first_connect'],
                "is_admin" => $user['is_admin'],
                "user_active" => $user['user_active'],
                "user_name" => $user['user_name']
            ]);
        }else{
            $this->send_JSON_error("Identifiant inccorrect");
        }
    }

    public function update_user(string $email, string $name_column, string $value):void
    {
        if (!verification_update_user($name_column)){
            $this->send_JSON_error("Erreur de colonne");
        }else{
            if ($name_column === "password"){
                $user = $this->userManager->get_user_by_email($email);
                $value = hash_mdp($value);
                if ($user['password'] === $value){
                    $this->send_JSON_error("Mot de passe déjà efficient");
                    exit();
                }
            }
            if ($this->userManager->update_user($email, $name_column, $value)){
                $this->send_JSON_OK();
            }else{
                $this->send_JSON_error("Erreur lors de l'update");
            }
        }
    }
}