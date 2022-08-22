<?php

require_once "./models/bdd.model.php";


class PartnerManager extends Bdd{
    private $partners;

    public function get_all_partner()
    {
        $req = "SELECT * FROM partner 
                LEFT JOIN users 
                ON partner.users_id = users.users_id";

        $stmt = $this->getBdd()->prepare($req);
        $stmt->execute();
        $all_partner = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $all_partner;
    }


}