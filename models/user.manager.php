<?php

require_once "bdd.model.php";


class UserManager extends Bdd{


    /**
     * this function say true or false email is available
     * @param string $email
     * @return bool
     */
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

    /**
     * this function get user by user email
     * @param string $email
     * @return array|null
     */
    public function get_user_by_email(string $email) : array | null
    {
        $req = "SELECT * FROM user WHERE email = :email";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result[0];
    }

    /**
     * this function get user by user id
     * @param int $id
     * @return array
     */
    public function get_user_by_id(int $id):array
    {
        $req = "SELECT * FROM user WHERE id = :id";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result[0];
    }

    /**
     * this function return is true or false create user
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function create_user(string $email, string $password) : bool
    {
        $req = "INSERT INTO user (`email`,`user_active` , `password`, `user_name`, `first_connect`,`is_admin`) 
                VALUES (:email,1 , :u_password, 'defaultName', 1, 0)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":u_password", $password, PDO::PARAM_STR);
        $stmt->execute();
        $est_ajouter = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_ajouter;
    }

    public function update_user(string $email,string $user_name, int $user_active, int $first_connect, int $is_admin):bool
    {
        $req="UPDATE user SET user_active= :user_active, user_nam= :user_name, first_connect= :first_connect, is_admin= :is_admin
            WHERE email = :email";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":user_active", $user_active, PDO::PARAM_INT);
        $stmt->bindValue(":user_name", $user_name, PDO::PARAM_STR);
        $stmt->bindValue(":first_connect", $first_connect, PDO::PARAM_STR);
        $stmt->bindValue(":is_admin", $is_admin, PDO::PARAM_INT);
        $stmt->bindValue(":email", $email, PDO::PARAM_INT);
        $stmt->execute();
        $est_update = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_update;
    }

    public function update_user_pass(string $email, string $password):bool
    {
        $req="UPDATE user SET password= :u_password WHERE email = :email";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":u_password", $password, PDO::PARAM_STR);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $est_update = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_update;
    }
}