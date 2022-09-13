<?php


abstract class Bdd
{
    private static $myPdo;

    private static function setBdd(): void
    {
        $dbname = "ecf_php";
        $idBdd = "root";
        $password = "Warolucky24";
        $port = 3306;
        $host = "localhost";

        self::$myPdo = new PDO("mysql:host=".$host.";dbname=".$dbname.";port=".$port, $idBdd, $password);
        self::$myPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    protected function getBdd()
    {
        if (self::$myPdo === null) {
            self::setBdd();
        }
        return self::$myPdo;
    }
}

