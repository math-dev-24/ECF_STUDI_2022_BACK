<?php

namespace App\Models;


abstract class Bdd
{
    private static $pdo;

    private static function setBdd()
    {
        $dbname = "ecf_php";
        $identifiant = "root";
        $password = "Warolucky24";
        $port = 3306;
        $host = "localhost";


        self::$pdo = new PDO("mysql:host=".$host.";dbname=".$dbname.";port=".$port, $identifiant, $password);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    protected function getBdd()
    {
        if (self::$pdo === null) {
            self::setBdd();
        }
        return self::$pdo;
    }
}