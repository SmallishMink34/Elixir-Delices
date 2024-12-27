<?php
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

$type = $_GET['t'] ?? '';
if (empty($type)) {
    $type = 'bois';
}

// Préparation de la requête SQL avec LIKE pour l'autocomplétion
if ($type == 'bois') {
    $stmt = $pdo->prepare("SELECT titre FROM Recette WHERE lower(titre) LIKE lower(:query) LIMIT 10");
} else {
    $stmt = $pdo->prepare("SELECT nom as titre FROM Ingredient WHERE lower(nom) LIKE lower(:query) LIMIT 10");
}
$stmt->execute(['query' => '%' . $query . '%']);



// Récupération des résultats
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Retourner les résultats au format JSON
header('Content-Type: application/json');
echo json_encode($results);
?>
