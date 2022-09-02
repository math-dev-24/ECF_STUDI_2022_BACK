<?php

require_once "bdd.model.php";

class GestionManager extends Bdd
{

    /**
     * this function return id last gestion create. all droit in false
     * @return bool|array
     */
    public function create_gestion() : bool | array
    {
        $req = "INSERT INTO gestion (`v_vetement`,`v_boisson`,`c_particulier`, `c_crosstrainning`,`c_pilate`)
            VALUE (0, 0, 0, 0, 0)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->execute();
        $stmt->closeCursor();

        $req = "SELECT g.id FROM gestion g ORDER BY g.id DESC LIMIT 1";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->execute();
        $id = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $id;
    }

    /**
     * this function create gestion by idem droit this partner
     * @param array $partner
     * @return int|array
     */
    public function create_gestion_by_partner(array $partner) : int | array
    {
        $req = "INSERT INTO gestion (`v_vetement`,`v_boisson`,`c_particulier`, `c_crosstrainning`,`c_pilate`)
            VALUE (:v_vetement, :v_boisson, :c_particulier, :c_pilate, :c_crosstrainning)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":v_vetement", $partner['v_vetement'], PDO::PARAM_INT);
        $stmt->bindValue(":v_boisson", $partner['v_boisson'], PDO::PARAM_INT);
        $stmt->bindValue(":c_particulier", $partner['c_particulier'], PDO::PARAM_INT);
        $stmt->bindValue(":c_pilate", $partner['c_pilate'], PDO::PARAM_INT);
        $stmt->bindValue(":c_crosstrainning", $partner['c_crosstrainning'], PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();

        $req = "SELECT g.id FROM gestion g ORDER BY g.id DESC LIMIT 1";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->execute();
        $id = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $id;

    }

    public function update_gestion_by_droitname_droitid(int $gestion_id, string $gestion_name, int $gestion_active):bool
    {
        $req = "UPDATE gestion SET ".$gestion_name." = :gestion_active WHERE id = :gestion_id";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":gestion_active", $gestion_active, PDO::PARAM_INT);
        $stmt->bindValue(":gestion_id", $gestion_id, PDO::PARAM_INT);
        $stmt->execute();
        $est_update = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_update;
    }
}