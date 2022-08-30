<?php

require_once "bdd.model.php";

class PartnerManager extends Bdd
{
    /**
     * this function return array all partner
     * @return array|null
     */
    public function get_all_partner() : array | null
    {
        $req = "SELECT 
                    u.email,
                    p.partner_name,
                    p.partner_active,
                    p.partner_name,
                    u.user_name,
                    p.logo_url,
                    p.id,
                    p.user_id
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

    /**
     * @param int $partner_id
     * @return array|null
     */
    public function  get_by_partnerId(int $partner_id) : array |null
    {
        $req = "SELECT 
        p.id,
        p.partner_name,
        p.partner_active,
        p.logo_url,
        p.user_id,
        g.v_vetement,
        g.v_boisson,
        g.c_particulier,
        g.c_crosstrainning,
        g.c_pilate
                FROM partner p
                INNER JOIN gestion g 
                    ON p.gestion_id = g.id
                WHERE p.id = $partner_id
                ";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->execute();
        $data_partner = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data_partner[0];
    }
    public function get_gestionId_by_partnerId(int $partner_id): array | null
    {
        $req = "SELECT p.gestion_id
                FROM partner p
                WHERE p.id = $partner_id
                ";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->execute();
        $data_partner = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data_partner[0];
    }


    /**
     * this function create_partner , this droit is all in false
     * @param int $user_id
     * @param string $partner_name
     * @param int $partner_active
     * @param int $gestion_id
     * @return bool
     */
    public function create_partner(int $user_id,string $partner_name, int $partner_active, int $gestion_id) : bool
    {
        $req = "INSERT INTO partner (`user_id`,`partner_name`,`partner_active`,`gestion_id`, `logo_url`) 
            VALUE (:user_id, :partner_name, :partner_active, :gestion_id, :logo_url)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":user_id",$user_id, PDO::PARAM_INT);
        $stmt->bindValue(":partner_name", $partner_name, PDO::PARAM_STR);
        $stmt->bindValue(":partner_active", $partner_active, PDO::PARAM_INT);
        $stmt->bindValue(":gestion_id",$gestion_id, PDO::PARAM_INT);
        $stmt->bindValue(":logo_url","https://logodix.com/logo/1060922.jpg", PDO::PARAM_STR);
        $stmt->execute();
        $est_ajouter = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_ajouter;
    }

    /**
     * @param int $partner_id
     * @param int $partner_active
     * @return bool
     */
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

    /**
     * @param int $partner_id
     * @param string $partner_name
     * @param int $partner_active
     * @param int $logo_url
     * @return bool
     */
    public function update_partner(int $partner_id,string $partner_name, int $partner_active, int $logo_url):bool
    {
        $req = "UPDATE partner SET partner_name = :partner_name,partner_active = :partner_active,logo_url = :logo_url
               WHERE partner.id = :partner_id";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(':partner_name', $partner_name, PDO::PARAM_STR);
        $stmt->bindValue(':partner_active', $partner_active, PDO::PARAM_INT);
        $stmt->bindValue(':logo_url', $logo_url, PDO::PARAM_STR);
        $stmt->bindValue(':partner_id', $partner_id, PDO::PARAM_INT);
        $stmt->execute();
        $est_update = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_update;
    }
}




