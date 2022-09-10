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

//Partner --------------------------------------------------------------------------------------------------------------------------------


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
            if($partner_active === 0){
                $structure = $this->structManager->get_by_partnerId($partner_id);
                foreach ($structure as $struct){
                    if ($struct['struct_active'] === 1){
                        $this->structManager->update_active($struct['id'],0);
                    }
                }
            }
            $this->get_partner_by_partnerId($partner_id);
        }else{
            $this->send_JSON_error("Erreur lors de l'update");
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
    public function update_partner(int $partner_id,string $partner_name, string $logo_url):void
    {
        $partner = $this->partnerManager->get_by_partnerId($partner_id);
        if ($partner['partner_name'] === $partner_name && $partner['logo_url']=== $logo_url){
            $this->send_JSON_OK();
            exit();
        }
        $this->partnerManager->update_partner($partner_id,$partner_name,$logo_url);
        $this->get_partner_by_partnerId($partner_id);
    }
    public function delete_partner(int $partner_id):void
    {
        $this->send_JSON_error(["error"=> "demande suppresion struct".$partner_id.". Route afaire"]);
    }
//struct -----------------------------------------------------------------------------------------------------------------



    public function update_droit_struct(int $struct_id, string $gestion_name, int $gestion_active):void
    {
        if (verification_gestion_name($gestion_name)){
            $structure = $this->structManager->get_by_structId($struct_id);
            $partner = $this->partnerManager->get_by_partnerId($structure['partner_id']);
            if ($partner[$gestion_name] === 1){
                $this->gestionManager->update_gestion_by_droitname_droitid($structure['gestion_id'],$gestion_name, $gestion_active);
                $this->send_JSON_OK();
            }else{
                $this->send_JSON_error("Impossible de modifier le partner référent n'a pas l'autorisation");
            }
        }else{
            $this->send_JSON_error("Erreur du nom de la gestion modifié");
        }
    }

    public function update_active_struct(int $struct_id, int $struct_active):void
    {
        $structure = $this->structManager->get_by_structId($struct_id);
        $partner_lie = $this->partnerManager->get_by_partnerId($structure['partner_id']);

        if ($partner_lie['partner_active'] === 0){
            $this->send_JSON_error("Partenaire référent non Actif. Impossible d'activé la structure.");
            exit();
        }
        if($this->structManager->update_active($struct_id,$struct_active)){
            $this->send_JSON(["ok" => "ok"]);
        }else{
            $this->send_JSON_error("Erreur lors de la création");
        }
    }
    public function update_struct(int $struct_id, string $struct_name):void
    {
        $struct = $this->structManager->get_by_structId($struct_id);
        if ($struct['struct_name'] === $struct_name){
            $this->send_JSON_OK();
            exit();
        }
        if ($this->structManager->update_struct($struct_id,$struct_name)){
            $this->get_struct_by_structId($struct_id);
        }else{
            $this->send_JSON_error("Impossible d'update");
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

}