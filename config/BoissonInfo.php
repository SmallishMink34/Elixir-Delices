<?php 
require '../include/db.php';
// Connexion à la base de données
$pdo = getDatabaseConnection();
if (!$pdo) {
    echo "probleme de connexion";
    throw new Exception('Failed to connect to the database.');
}

$query = $_GET['drink'] ?? '';
if (empty($query)) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT r.titre, r.preparation, ri.quantite, i.nom FROM Recette r join Recette_Ingredient ri on ri.recette_id = r.id join Ingredient i on i.id = ri.ingredient_id  WHERE r.id = :query");
$stmt->execute(['query' => $query]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');

echo json_encode($result);
?>