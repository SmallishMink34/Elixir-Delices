<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../include/db.php';
// Connexion à la base de données
$pdo = getDatabaseConnection();
if (!$pdo) {
    echo "probleme de connexion";
    throw new Exception('Failed to connect to the database.');
}

// Récupération du paramètre de recherche
$query = $_GET['q'] ?? '';
if (empty($query)) {
    echo json_encode([]);
    exit;
}
// Préparation de la requête SQL avec LIKE pour l'autocomplétion
$stmt = $pdo->prepare("SELECT titre FROM Recette WHERE lower(titre) LIKE lower(:query) LIMIT 10");
$stmt->execute(['query' => '%' . $query . '%']);



// Récupération des résultats
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Retourner les résultats au format JSON
header('Content-Type: application/json');
echo json_encode($results);
?>
