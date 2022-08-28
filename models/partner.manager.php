<?php

require_once "bdd.model.php";

class PartnerManager extends Bdd
{
    public function get_all_partner() : array | null
    {
        $req = "SELECT  p.id, p.partner_name, p.partner_active, p.logo_url FROM partner p";
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
                INNER JOIN struct s
                ON s.partner_id = p.id
                INNER JOIN gestion g 
                ON p.gestion_id = g.id
                WHERE p.id = $partner_id
                ";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->execute();
        $data_partner = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data_partner;
    }

    public function create_partner(int $user_id,string $partner_name, int $partner_active, int $gestion_id) : bool
    {
        $req = "INSERT INTO partner (`user_id`,`partner_name`,`partner_active`,`gestion_id`, `logo_url`) 
            VALUE (:user_id, :partner_name, :partner_active, :gestion_id, :logo_url)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":user_id",$user_id, PDO::PARAM_INT);
        $stmt->bindValue(":partner_name", $partner_name, PDO::PARAM_STR);
        $stmt->bindValue(":partner_active", $partner_active, PDO::PARAM_INT);
        $stmt->bindValue(":gestion_id",$gestion_id, PDO::PARAM_INT);
        $stmt->bindValue(":logo_url","", PDO::PARAM_STR);
        $stmt->execute();
        $est_ajouter = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_ajouter;
    }
    public function update_active(int $partner_id,int $partner_active):void
    {
        $req = "UPDATE partner SET partner_active = :partner_active WHERE partner.id = :partner_id";
        $stmt = $this->getBdd()->preparer($req);
        $stmt->bindValue(':partner_active', $partner_active, PDO::PARAM_INT);
        $stmt->bindValue(':partner_id', $partner_id, PDO::PARAM_INT);
        $stmt->excute();
        $stmt->closeCursor();
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