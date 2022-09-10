<?php

use JetBrains\PhpStorm\NoReturn;

class UserController
{

    #[NoReturn] public function getAllUser():void
    {
        $this->send_JSON($this->userManager->get_all_user());
        exit();
    }

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
        $user =$this->userManager->get_user_by_email($email);

        if (!verification_update_user($name_column)){
            $this->send_JSON_error("Erreur de colonne");
        }else{
            if ($name_column === "user_name"){
                if ($user['user_name'] === $value){
                    $this->send_JSON_OK();
                    exit();
                }
            }
            if ($name_column === "password"){
                if ($user['password'] === $value){
                    $this->send_JSON_OK();
                    exit();
                }
            }
            if ($this->userManager->update_user($email, $name_column, $value)){
                $this->send_JSON($user);
            }else{
                $this->send_JSON_error("Erreur lors de l'update");
            }
        }
    }
}