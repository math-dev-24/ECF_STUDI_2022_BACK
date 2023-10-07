<?php

require_once "BddModel.php";

class LogManager extends Bdd
{

    function addLog(string $message) : bool
    {
        $req = "INSERT INTO log ('description') VALUE (:description)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(':description',$message, PDO::PARAM_STR);
        $stmt->execute();
        $est_ajouter = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $est_ajouter;
    }
}