<?php

require_once "bdd.model.php";

class GestionManager extends Bdd
{
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

    public function create_gestion_by_partner(array $partner) : bool | array
    {
        $req = "INSERT INTO gestion (`v_vetement`,`v_boisson`,`c_particulier`, `c_crosstrainning`,`c_pilate`)
            VALUE (:v_vetement, :v_boisson, :c_particulier, :c_pilate, :c_crosstrainning)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt6>bindValue(":v_vetement", $partner['v_vetement'], PDO::PARAM_INT);
        $stmt6>bindValue(":v_boisson", $partner['v_boisson'], PDO::PARAM_INT);
        $stmt6>bindValue(":c_particulier", $partner['c_particulier'], PDO::PARAM_INT);
        $stmt6>bindValue(":c_pilate", $partner['c_pilate'], PDO::PARAM_INT);
        $stmt6>bindValue(":c_crosstrainning", $partner['c_crosstrainning'], PDO::PARAM_INT);
        $stmt->execute();
        $est_ajouter = ($stmt->rowCount() > 0);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        if($est_ajouter){
            return $data;
        }else{
            return false;
        }
    }
}