<?php

require_once "./models/partner.manager.php";
require_once "./models/user.manager.php";
require_once "./models/struct.manager.php";
require_once "./models/gestion.manager.php";
require_once "function.php";

class ApiController{
    private $partnerManager;
    private $userManager;
    private $structManager;
    private $gestionManager;

    public function __construct()
    {
        $this->partnerManager = new PartnerManager;
        $this->userManager = new UserManager;
        $this->structManager = new StructManager;
        $this->gestionManager = new GestionManager;
    }
    public function sendJSON($infos): void
    {
        echo json_encode($infos, JSON_UNESCAPED_UNICODE);
    }
    public function sendJSONError($info):void
    {
        $data = ["error" => $info];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    public  function get_all_partner(): void
    {
        $this->sendJSON($this->partnerManager->get_all_partner());
    }
    public function get_all_struct(): void
    {
        $this->sendJSON($this->structManager->get_all_struct());
    }
    public function get_partner_by_partnerId(int $partnerId):void
    {
        $partner = $this->partnerManager->get_by_partnerId($partnerId);
        $user = $this->userManager->get_user_by_id($partner['user_id'])[0];
        $struct = $this->structManager->get_by_partnerId($partner['id']);
        $this->sendJSON([
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
    public function get_struct_by_structId(int $structId):void
    {
        $this->sendJSON($this->structManager->get_by_structId($structId));
    }
    public function go_authentification(string $email, string $password): void
    {
        $user = $this->userManager->get_user_by_email($email);
        if($user[0]['password'] === $password){
            $this->sendJSON([
                "id" => $user[0]['id'],
                "email" => $user[0]["email"],
                "first_connect" => $user[0]['first_connect'],
                "is_admin" => $user[0]['is_admin'],
                "user_active" => $user[0]['user_active'],
                "user_name" => $user[0]['user_name']
            ]);
        }else{
            $this->sendJSON(["error" =>"Erreur d'authentification"]);
        }
    }
    public function create_partner(string $partner_name, string $user_email, int $partner_active):void
    {
        $password =cryptageMdp($partner_name);
        if(!$this->userManager->email_is_available($user_email)){
            $this->sendJSONError("Email non disponnible");
        }else{
            if ($this->userManager->create_user($user_email, $password)){
                $user = $this->userManager->get_user_by_email($user_email);
                $gestion_id = $this->gestionManager->create_gestion();
                $partner = $this->partnerManager->create_partner($user[0]['id'],$partner_name, $partner_active, $gestion_id['id']);
                //Tools::sendMail($user_email,"inscription" ,"Bonjour, Vous êtes maintenant inscrit en temps que partenaire. Voici votre mot de passe : ".$user['password'].". Il est à changer dès la première connexion. Merci Bonne journée");
                $this->sendJSON($partner);
            }else{
                $this->sendJSONError("Erreur lors de la création de l'utilisateur");
            }
        }
    }
    public function create_struct(string $user_email, string $struct_name, int $struct_active, int $partner_id):void
    {
        $password = cryptageMdp($struct_name);
        if ($this->userManager->email_is_available($user_email)){
            $this->sendJSONError("Email non disponnible");
        }else{
            if(!$this->userManager->create_user($user_email,$password)){
               $this->sendJSONError("Erreurs lors de la création du compte");
            }else{
                $user = $this->userManager->get_user_by_email($user_email);
                $partner = $this->partnerManager->get_by_partnerId($partner_id);
                $gestion_id = $this->gestionManager->create_gestion_by_partner($partner);
                if($gestion_id){
                    $this->structManager->create_struct($user[0]["id"], $struct_name, $struct_active, $gestion_id['id'],$partner_id);
                    //Tools::sendMail($user_email, "inscription","Bonjour, Vous êtes maintenant inscrit en temps que structure. Voici votre mot de passe : ".$user['password'].". Il est à changer dès la première connexion. Merci Bonne journée");
                }else{
                    $this->sendJSON(["error" => "Erreur lors de la création de gestion"]);
                }
            }
        }
    }
    public function update_active_partner(int $partner_id, int $partner_active):void
    {
        if($this->partnerManager->update_active($partner_id, $partner_active)){
            $this->sendJSON(["ok" => "ok"]);
        }else{
            $this->sendJSONError("Erreur lors de la création");
        }
    }
    public function update_partner(int $partner_id,string $partner_name, int $partner_active, int $logo_url):void
    {
        $this->partnerManager->update_partner($partner_id,$partner_name,$partner_active,$logo_url);
    }
    public function delete_partner(int $partner_id):void
    {
        $this->sendJSON(["error"=> "demande suppresion struct".$partner_id.". Route afaire"]);
    }
    public function update_active_struct(int $struct_id, int $struct_active):void
    {
        if($this->structManager->update_active($struct_id,$struct_active)){
            $this->sendJSON(["ok" => "ok"]);
        }else{
            $this->sendJSONError("Erreur lors de la création");
        }
    }
    public function update_struct(int $struct_id, string $struct_name, string $struct_active):void
    {
        if ($this->structManager->update_struct($struct_id,$struct_name,$struct_active)){
            $this->sendJSONError(['ok' => "ok"]);
        }else{
            $this->sendJSONError("Erreur lors de la modification");
        }
    }
    public function delete_struct(int $struct_id):void
    {
        $this->sendJSON(["msg"=> "demande suppresion struct".$struct_id]);
    }
}