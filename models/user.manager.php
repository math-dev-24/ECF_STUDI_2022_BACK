<?php

require_once "bdd.model.php";


class UserManager extends Bdd{

    public function email_is_available(string $email)
    {
        $req = "SELECT * FROM user u WHERE u.email = :email";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $is_available = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return !$is_available;
    }

    public function get_user_by_email(string $email) : array | null
    {
        $req = "SELECT * FROM user WHERE email = :email";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }

    public function create_user(string $email, string $password) : bool
    {
        $req = "INSERT INTO user (`email`, `user_active`, `password`, `first_connect`,`is_admin`) 
                VALUES (:email, 1, :u_password, 1, 0)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":u_password", $password, PDO::PARAM_STR);
        $stmt->execute();
        $est_ajouter = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_ajouter;
    }
}