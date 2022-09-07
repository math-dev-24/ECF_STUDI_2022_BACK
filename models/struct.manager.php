<?php

require_once "bdd.model.php";

class StructManager extends Bdd
{

    /**
     * this function get all structure
     * @return array|null
     */
    public function get_all_struct(): array | null
    {
        $req = "SELECT s.id, s.struct_name, s.struct_active, s.partner_id ,p.partner_name, p.logo_url, u.user_name, u.email FROM struct s
                LEFT JOIN partner p ON p.id = s.partner_id LEFT JOIN user u ON s.user_id = u.id
        ";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->execute();
        $all_struct = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $all_struct;
    }

    /**
     * this function get data of struct by partner id
     * @param int $partner_id
     * @return array|null
     */
    public function get_by_partnerId(int $partner_id): array | null
    {
        $req = "SELECT s.struct_name,s.gestion_id, s.struct_active, s.id, g.v_vetement, g.v_boisson, g.c_particulier, g.c_crosstrainning, g.c_pilate 
                FROM struct s 
                INNER JOIN gestion g ON s.gestion_id = g.id
                WHERE s.partner_id = :partner_id
            ";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":partner_id",$partner_id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    /**
     * this function get data of struct  with his id
     * @param int $id
     * @return array|null
     */
    public function get_by_structId(int $id) : array | null
    {
        $req = "SELECT *
                FROM struct s
                INNER JOIN gestion g
                ON s.gestion_id = g.id
                WHERE s.id = $id
                ";
        $stmt = $this->getBdd()->prepare($req);
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
    public function create_struct(int $user_id, string $struct_name, int $struct_active,int $gestion_id, int $partner_id) : bool
    {
        $req = "INSERT INTO struct (`user_id`,`partner_id`,`struct_name`,`struct_active`,`gestion_id`) 
            VALUE (:user_id, :partner_id, :struct_name, :struct_active, :gestion_id)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":user_id",$user_id, PDO::PARAM_INT);
        $stmt->bindValue(":partner_id", $partner_id, PDO::PARAM_INT);
        $stmt->bindValue(":struct_name", $struct_name, PDO::PARAM_STR);
        $stmt->bindValue(":struct_active",$struct_active, PDO::PARAM_INT);
        $stmt->bindValue(":gestion_id",$gestion_id, PDO::PARAM_INT);
        $stmt->execute();
        $est_ajouter = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_ajouter;

    }

    /**
     * this function update active status struct
     * @param int $struct_id
     * @param int $struct_active
     * @return bool
     */
    public function update_active(int $struct_id,int $struct_active):bool
    {
        $req = "UPDATE struct SET struct_active = :struct_active WHERE struct.id = :struct_id";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(':struct_active', $struct_active, PDO::PARAM_INT);
        $stmt->bindValue(':struct_id', $struct_id, PDO::PARAM_INT);
        $stmt->execute();
        $est_update = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_update;
    }

    /**
     * this function update name struct
     * @param int $struct_id
     * @param string $struct_name
     * @return bool
     */
    public function update_struct(int $struct_id,string $struct_name):bool
    {
        $req = "UPDATE struct s SET struct_name = :struct_name WHERE s.id = :struct_id";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(':struct_name', $struct_name, PDO::PARAM_STR);
        $stmt->bindValue(':struct_id', $struct_id, PDO::PARAM_INT);
        $stmt->execute();
        $est_update = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_update;
    }

}