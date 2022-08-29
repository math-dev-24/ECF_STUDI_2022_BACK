<?php

require_once "bdd.model.php";

class StructManager extends Bdd
{

    public function get_all_struct(): array | null
    {
        $req = "SELECT * FROM struct";

        $stmt = $this->getBdd()->prepare($req);
        $stmt->execute();
        $all_struct = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $all_struct;
    }
    public function get_by_partnerId(int $partner_id): array | null
    {
        $req = "SELECT * FROM struct s 
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
        return $data_struct;
    }

    public function create_struct(int $user_id, string $struct_name, int $struct_active,int $gestion_id, int $partner_id) : bool
    {
        $req = "INSERT INTO partner (`user_id`,`partner_id`,`struct_name`,`struct_active`,`gestion_id`) 
            VALUE (:user_id, :partner_id, :struct_name, :struct_active, :gestion_id)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":user_id",$user_id, PDO::PARAM_INT);
        $stmt->bindValue(":partner_id",$partner_id, PDO::PARAM_INT);
        $stmt->bindValue(":struct_name", $struct_name, PDO::PARAM_STR);
        $stmt->bindValue(":partner_active", $struct_active, PDO::PARAM_INT);
        $stmt->bindValue(":gestion_id",$gestion_id, PDO::PARAM_INT);
        $stmt->execute();
        $est_ajouter = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_ajouter;
    }

    public function update_active(int $struct_id,int $struct_active):void
    {
        $req = "UPDATE struct SET struct_active = :struct_active WHERE struct.id = :struct_id";
        $stmt = $this->getBdd()->preparer($req);
        $stmt->bindValue(':struct_active', $struct_active, PDO::PARAM_INT);
        $stmt->bindValue(':struct_id', $struct_id, PDO::PARAM_INT);
        $stmt->excute();
        $stmt->closeCursor();
    }
    public function update_struct(int $struct_id,string $struct_name,int $struct_active):void
    {
        $req = "UPDATE struct 
                SET struct_active = :struct_active,
                struct_name = :struct_name                
                WHERE struct.id = :struct_id";
        $stmt = $this->getBdd()->preparer($req);
        $stmt->bindValue(':struct_name', $struct_name, PDO::PARAM_STR);
        $stmt->bindValue(':struct_active', $struct_active, PDO::PARAM_INT);
        $stmt->bindValue(':struct_id', $struct_id, PDO::PARAM_INT);
        $stmt->excute();
        $stmt->closeCursor();
    }

}