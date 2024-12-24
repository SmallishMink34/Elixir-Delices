<?php
include_once __DIR__ . '/../config/mdp.php';

function getDatabaseConnection(): PDO {
//    $host = "sql112.byethost16.com";
//    $username = "b16_37711670";
//    $password = "zdP97PHxrKf&GQ8h";
//    $dbname = "b16_37711670_Boissons";

    $host = "localhost";
    $username = "root";
    $password = "1002";
    $dbname = "elixir_delices";
    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("USE $dbname");
        return $pdo;
    } catch (PDOException $e) {
        echo "host : $host, username : $username, password : $password, dbname : $dbname";
        die('Erreur de connexion : ' . $e->getMessage());
    }
}
?>
