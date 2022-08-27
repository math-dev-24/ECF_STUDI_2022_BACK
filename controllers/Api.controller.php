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
        $this->sendJSON($this->partnerManager->get_by_partnerId($partnerId));
    }
    public function get_struct_by_structId(int $structId):void
    {
        $this->sendJSON($this->structManager->get_by_structId($structId));
    }
    public function go_authentification(string $email, string $password): void
    {
        $user = $this->userManager->get_user_by_email($email);
        if($user['password'] === $password && $user['active'] === 1){
            $this->sendJSON(['state' => true]);
        }else{
            $this->sendJSON(['state' => false]);
        }
    }
    public function create_partner(string $partner_name, string $user_email, int $partner_active):void
    {
        $password =cryptageMdp($partner_name);
        try {
            if(!$this->userManager->create_user($user_email, $password)){
                throw new Exception("Erreur lors de la création du compte");
            }else{
                $user = $this->userManager->get_user_by_email($user_email);
                $gestion_id = $this->gestionManager->create_gestion();
                if($gestion_id){
                    $this->partnerManager->create_partner($user['id'],$partner_name, $partner_active, $gestion_id['id']);
                    Tools::sendMail($user_email,"inscription" ,"Bonjour, Vous êtes maintenant inscrit en temps que partenaire. Voici votre mot de passe : ".$user['password'].". Il est à changer dès la première connexion. Merci Bonne journée");
                }else{
                    throw new Exception("Erreur lors de la création de gestion");
                }
            }
        }
        catch (Exception $e){
            $this->sendJSON($e);
        }
    }
    public function create_struct(string $user_email, string $struct_name, int $struct_active, int $partner_id):void
    {
        $password = cryptageMdp($struct_name);
        try {
            if(!$this->userManager->create_user($user_email,$password)){
                throw new Exception("Erreurs lors de la création du compte");
            }else{
                $user = $this->userManager->get_user_by_email($user_email);
                $partner = $this->partnerManager->get_by_partnerId($partner_id);
                $gestion_id = $this->gestionManager->create_gestion_by_partner($partner);
                if($gestion_id){
                    $this->structManager->create_struct($user["id"], $struct_name, $struct_active, $gestion_id['id'],$partner_id);
                    Tools::sendMail($user_email, "inscription","Bonjour, Vous êtes maintenant inscrit en temps que structure. Voici votre mot de passe : ".$user['password'].". Il est à changer dès la première connexion. Merci Bonne journée");
                }else{
                    throw new Exception("Erreur lors de la création de gestion");
                }
            }
        }
        catch (Exception $e)
        {
            $this->sendJSON($e);
        }

    }
}