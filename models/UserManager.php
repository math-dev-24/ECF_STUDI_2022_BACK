<?php


require_once "BddModel.php";

class UserManager extends Bdd
{

    public function getAllUser():array
    {
        $req = "SELECT * FROM user";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }

    /**
     * this function say true or false email is available
     * @param string $email
     * @return bool
     */
    public function emailIsAvailable(string $email): bool
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
    public function getUserByEmail(string $email): array|null
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
     * this function return is true or false create user
     * @param string $email
     * @param string $userName
     * @param string $password
     * @return bool
     */
    public function createUser(string $email,string $userName, string $password): bool
    {
        $req = "INSERT INTO user (`email`,`user_active` , `password`, `user_name`, `first_connect`,`is_admin`) 
                VALUES (:email,1 , :u_password, :u_name, 1, 0)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":u_password", $password, PDO::PARAM_STR);
        $stmt->bindValue(":u_name", $userName, PDO::PARAM_STR);
        $stmt->execute();
        $isAdd = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $isAdd;
    }


    public function updateUser(string $email, string $column, string $value): bool
    {
        $req = "UPDATE user SET " . $column . " = :u_value WHERE email = :email";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":u_value", $value, PDO::PARAM_STR);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $isUpdate = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $isUpdate;
    }

}