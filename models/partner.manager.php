<?php

require_once "bdd.model.php";

class PartnerManager extends Bdd
{
    public function get_all_partner() : array | null
    {
        $req = "SELECT 
                    u.email,
                    p.partner_name,
                    p.partner_active,
                    p.partner_name,
                    u.user_name,
                    p.logo_url,
                    p.id
                FROM partner p
                INNER JOIN user u
                ON u.id = p.user_id
                ";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->execute();
        $all_partner = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $all_partner;
    }

    public function  get_by_partnerId(int $partner_id) : array |null
    {
        $req = "SELECT *
                FROM partner p
                INNER JOIN gestion g ON p.gestion_id = g.id
                WHERE p.id = $partner_id
                ";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->execute();
        $data_partner = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data_partner[0];
    }

    public function create_partner(int $user_id,string $partner_name, int $partner_active, int $gestion_id) : array | string
    {
        $req = "INSERT INTO partner (`user_id`,`partner_name`,`partner_active`,`gestion_id`, `logo_url`) 
            VALUE (:user_id, :partner_name, :partner_active, :gestion_id, :logo_url)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":user_id",$user_id, PDO::PARAM_INT);
        $stmt->bindValue(":partner_name", $partner_name, PDO::PARAM_STR);
        $stmt->bindValue(":partner_active", $partner_active, PDO::PARAM_INT);
        $stmt->bindValue(":gestion_id",$gestion_id, PDO::PARAM_INT);
        $stmt->bindValue(":logo_url","none", PDO::PARAM_STR);
        $stmt->execute();
        $est_ajouter = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        if($est_ajouter){
            $req = "SELECT * FROM partner p ORDER BY p.id DESC LIMIT 1";
            $stmt = $this->getBdd()->prepare($req);
            $stmt->execute();
            $id = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $id;
        }else{
            return 0;
        }
    }
    public function update_active(int $partner_id,int $partner_active):bool
    {
        $req = "UPDATE partner SET partner_active = :partner_active WHERE id = :partner_id";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":partner_active", $partner_active, PDO::PARAM_INT);
        $stmt->bindValue(":partner_id", $partner_id, PDO::PARAM_INT);
        $stmt->execute();
        $est_update = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_update;
    }
    public function update_partner(int $partner_id,string $partner_name, int $partner_active, int $logo_url):void
    {
        $req = "UPDATE partner SET 
                   partner_name = :partner_name,
                   partner_active = :partner_active,
                   logo_url = :logo_url
               WHERE partner.id = :partner_id";
        $stmt = $this->getBdd()->preparer($req);
        $stmt->bindValue(':partner_name', $partner_name, PDO::PARAM_STR);
        $stmt->bindValue(':partner_active', $partner_active, PDO::PARAM_INT);
        $stmt->bindValue(':logo_url', $logo_url, PDO::PARAM_STR);
        $stmt->bindValue(':partner_id', $partner_id, PDO::PARAM_INT);
        $stmt->excute();
        $stmt->closeCursor();
    }
}