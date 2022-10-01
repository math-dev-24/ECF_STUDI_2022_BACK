<?php

require_once "./models/UserManager.php";
require_once "./core/JWT.php";

class UserController
{
    private UserManager $userManager;
    private JWT $jwt;

    public function __construct()
    {
        $this->userManager = new UserManager();
        $this->jwt = new JWT();
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
        }

        $user = $this->userManager->getUserByEmail($email);

        if ($user['user_active'] !== 1)
        {
            Render::sendJsonError("Utilisateur inactif.");
        }
        
        if($user['password'] === $password){
            $header = [
              "alg" => "HS256",
              "typ" => "JWT"
            ];

            $payload = [
                "id" => $user['id'],
                "email" => $user["email"],
                "user_name" => $user['user_name'],
                "is_admin" => $user['is_admin']
            ];
            $tokenJWT = $this->jwt->generate($header, $payload);

            $user = [
                "id" => $user['id'],
                "email" => $user["email"],
                "first_connect" => $user['first_connect'],
                "is_admin" => $user['is_admin'],
                "user_active" => $user['user_active'],
                "user_name" => $user['user_name'],
                "profil_url" => $user['profil_url']
            ];

            http_response_code(200);
            Render::sendJSON(["accessToken" => $tokenJWT, "user" => $user]);

        }else{
            http_response_code(400);
            Render::sendJsonError("Identification impossible");
        }
    }

    /**
     * @param string $token
     * @return void
     */
    public function goConnectWithToken(string $token):void
    {
        $payload = $this->jwt->getPayload($token);
        $user = $this->userManager->getUserByEmail($payload['email']);

        $user = [
            "id" => $user['id'],
            "email" => $user["email"],
            "first_connect" => $user['first_connect'],
            "is_admin" => $user['is_admin'],
            "user_active" => $user['user_active'],
            "user_name" => $user['user_name'],
            "profil_url" => $user['profil_url']
        ];
        Render::sendJSON(["token" => $token, "user" => $user]);
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