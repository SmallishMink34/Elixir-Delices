<?php
function getDatabaseConnection(): PDO {
    $host = "localhost";
    $username = "root";
    $password = "1002";
    $dbname = "elixir_delices";

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    try {
        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        die('Erreur de connexion : ' . $e->getMessage());
    }
}
?>
