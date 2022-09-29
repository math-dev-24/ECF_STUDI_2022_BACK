<?php

require_once "./models/UserManager.php";

class UserController
{
    private UserManager $userManager;

    public function __construct()
    {
        $this->userManager = new UserManager();
    }

    public function getAllUser():void
    {
        Render::sendJSON($this->userManager->getAllUser());
    }

    public function getHome():void
    {
        Render::sendJSON("Bienvenu sur l'api");
    }

    /**
     * @param string $email
     * @param string $password
     * @return void
     */
    public function goConnect(string $email, string $password): void
    {
        if (!Tools::verificationEmail($email)){
            Render::sendJsonError("Email non valide");
            exit();
        }

        $user = $this->userManager->getUserByEmail($email);

        if($user['password'] === $password){
            Render::sendJSON([
                "id" => $user['id'],
                "email" => $user["email"],
                "first_connect" => $user['first_connect'],
                "is_admin" => $user['is_admin'],
                "user_active" => $user['user_active'],
                "user_name" => $user['user_name'],
                "profil_url" => $user['profil_url']
            ]);
        }else{
            Render::sendJsonError("Identification impossible");
        }
    }


    public function updateUser(string $email, string $name_column, string $value):void
    {
        $user =$this->userManager->getUserByEmail($email);
        $userName = $user['user_name'];
        $passwordHash = Tools::hashMdp($value);

        if (!Tools::verificationUpdateUser($name_column)){
            Render::sendJsonError("Nom de colone à changer invalide");
        }else{

            if (
                ($name_column === "user_name" && $userName === $value) ||
                ($name_column === "password" && $user['password'] === $passwordHash) ||
                ($name_column === "profil_url" && $user['profil_url'] === $value)
            )
            {
                Render::sendJsonOK();
            }

            if ($name_column === "password")
            {
                if ($user['first_connect'] === 1)
                {
                    $this->userManager->updateUser($email, "first_connect", "0");
                }
                $this->userManager->updateUser($email, $name_column, $passwordHash);
                Render::sendJsonOK();
            }
            $this->userManager->updateUser($email, $name_column, $value);
            Render::sendJsonOK();
        }
    }
}