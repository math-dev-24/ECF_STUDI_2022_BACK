<?php

require "./models/partner/partner.manager.php";
require "./models/user/user.manager.php";

class ApiController{
    private $partnerManager;
    private $userManager;

    public function __construct()
    {
        $this->partnerManager = new PartnerManager;
        $this->userManager = new UserManager;
    }

    public  function get_All_partner()
    {
        echo Tools::return_json("méthode GET", $this->partnerManager->get_all_partner());
    }

    public function go_authentification(string $email, string $password){
        $user = $this->userManager->get_user_by_email($email);
        if($user['password'] === $password && $user['active'] === 1){
            echo Tools::return_json("méthode POST", ["connect" => true] );
        }else{
            echo Tools::return_json("méthode POST", ["connect" => false] );
        }

    }
}