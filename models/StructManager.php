<?php

require_once "BddModel.php";

class StructManager extends Bdd
{

    /**
     * this function get all structure
     * @return array
     */
    public function getAllStruct(): array
    {
        $req = "SELECT s.id, s.struct_name, s.struct_active, p.id partner_id, p.user_id partner_user_id, p.partner_name, p.logo_url,u.id user_id ,u.profil_url ,u.email, u.user_name, u.user_active
                FROM struct s
                LEFT JOIN partner p ON p.id = s.partner_id
                    LEFT JOIN user u ON s.user_id = u.id
        ";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->execute();
        $all_struct = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $all_struct;
    }

    /**
     * this function get data of struct by partner id
     * @param int $partnerId
     * @return array
     */
    public function getStructByPartnerId(int $partnerId): array
    {
        $req = "SELECT s.id, s.struct_name, s.struct_active,g.v_vetement, g.v_boisson, g.c_particulier, g.c_crosstrainning, g.c_pilate 
                FROM struct s 
                INNER JOIN gestion g ON s.gestion_id = g.id
                WHERE s.partner_id = :partner_id
            ";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":partner_id",$partnerId, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    public function getByUserId(int $userId):array
    {
        $req = "SELECT s.id, s.struct_name, s.struct_active, p.id partner_id, p.user_id partner_user_id, p.partner_name, p.logo_url,u.id user_id ,u.email, u.user_name, u.user_active
                FROM struct s
                LEFT JOIN partner p ON p.id = s.partner_id
                    LEFT JOIN user u ON s.user_id = u.id
                WHERE s.user_id = :u_id
        ";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":u_id", $userId, PDO::PARAM_INT);
        $stmt->execute();
        $struct = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $struct[0];
    }

    /**
     * this function get data of struct  with his id
     * @param int $id
     * @return array
     */
    public function getByStructId(int $id) : array
    {
        $req = "SELECT 
        s.id struct_id, 
        s.struct_name, 
        s.struct_active,
        p.id partner_id, 
        p.user_id partner_user_id, 
        p.partner_name, 
        p.partner_active,
        u.id user_id, 
        u.user_name, 
        u.email, 
        u.user_active,
        u.profil_url,
        g.id gestion_id, 
        g.v_vetement, 
        g.v_boisson, 
        g.c_crosstrainning, 
        g.c_particulier,
        g.c_pilate, 
        s.struct_address, 
        s.struct_city, 
        s.struct_postal
                FROM struct s
                INNER JOIN gestion g
                ON s.gestion_id = g.id
                INNER JOIN partner p
                ON p.id = s.partner_id
                INNER JOIN user u 
                ON u.id = s.user_id
                WHERE s.id = :s_id
                ";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(':s_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data_struct = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data_struct[0];
    }

    /**
     * this function create struct by defaut All droit is false
     * @param int $user_id
     * @param string $struct_name
     * @param int $struct_active
     * @param int $gestion_id
     * @param int $partner_id
     * @return bool
     */
    public function createStruct(int $user_id, string $struct_name, int $struct_active,int $gestion_id,
                                 int $partner_id, string $structAddress, string $structCity, int $structPostal) : bool
    {
        $req = "INSERT INTO struct (`user_id`,`partner_id`,`struct_name`,`struct_active`,`gestion_id`,`struct_address`, `struct_city`, `struct_postal`) 
            VALUE (:user_id, :partner_id, :struct_name, :struct_active, :gestion_id, :address, :city, :postal)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":user_id",$user_id, PDO::PARAM_INT);
        $stmt->bindValue(":partner_id", $partner_id, PDO::PARAM_INT);
        $stmt->bindValue(":struct_name", $struct_name, PDO::PARAM_STR);
        $stmt->bindValue(":struct_active",$struct_active, PDO::PARAM_INT);
        $stmt->bindValue(":gestion_id",$gestion_id, PDO::PARAM_INT);
        $stmt->bindValue(":address",$structAddress, PDO::PARAM_STR);
        $stmt->bindValue(":city",$structCity, PDO::PARAM_STR);
        $stmt->bindValue(":postal",$structPostal, PDO::PARAM_INT);
        $stmt->execute();
        $is_add = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $is_add;
    }

    /**
     * this function update active status struct
     * @param int $struct_id
     * @param int $struct_active
     * @return bool
     */
    public function updateActive(int $struct_id,int $struct_active):bool
    {
        $req = "UPDATE struct SET struct_active = :struct_active WHERE struct.id = :struct_id";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(':struct_active', $struct_active, PDO::PARAM_INT);
        $stmt->bindValue(':struct_id', $struct_id, PDO::PARAM_INT);
        $stmt->execute();
        $is_update = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $is_update;
    }

    /**
     * this function update name struct
     * @param int $struct_id
     * @param string $struct_name
     * @return bool
     */
    public function updateStruct(int $struct_id,string $struct_name, string $address, string $city, int $postal):bool
    {
        $req = "UPDATE struct s SET struct_name = :struct_name, struct_address = :address, struct_city = :city, struct_postal = :postal WHERE s.id = :struct_id";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(':struct_name', $struct_name, PDO::PARAM_STR);
        $stmt->bindValue(':struct_id', $struct_id, PDO::PARAM_INT);
        $stmt->bindValue(':address', $address, PDO::PARAM_STR);
        $stmt->bindValue(':city', $city, PDO::PARAM_STR);
        $stmt->bindValue(':postal', $postal, PDO::PARAM_INT);
        $stmt->execute();
        $is_update = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $is_update;
    }


}