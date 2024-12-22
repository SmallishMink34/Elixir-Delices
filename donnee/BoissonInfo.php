<?php 
require '../include/db.php';
require '../include/utils.php';
// Connexion à la base de données
$pdo = getDatabaseConnection();
if (!$pdo) {
    echo "probleme de connexion";
    throw new Exception('Failed to connect to the database.');
}

$getValue = $_GET['drink'] ?? '';
if (empty($getValue)) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT r.titre, r.preparation, ri.quantite, ri.unite, i.nom, i.id FROM Recette r join Recette_Ingredient ri on ri.recette_id = r.id join Ingredient i on i.id = ri.ingredient_id  WHERE r.id = :query");
$stmt->execute(['query' => $getValue]);
$query = $stmt->fetchAll(PDO::FETCH_ASSOC);

$requeteNeighborg = $pdo->prepare("  SELECT * FROM (
                                                SELECT 
                                                LAG(id) OVER (ORDER BY id) AS previous_id,
                                                LAG(titre) OVER (ORDER BY id) AS previous_name,
                                                id AS current_id,
                                                titre AS current_name,
                                                LEAD(id) OVER (ORDER BY id) AS next_id,
                                                LEAD(titre) OVER (ORDER BY id) AS next_name
                                                FROM recette
                                            ) subquery
                                            WHERE current_id = :query;");
$requeteNeighborg->execute(['query' => $getValue]);
$Neighborg = $requeteNeighborg->fetch(PDO::FETCH_ASSOC);

$result = [];

if (!empty($query)) {
    $result['titre'] = $query[0]['titre'] ?? null;
    $result['titreFormat'] = formatTitre($result['titre']);
    $result['preparation'] = $query[0]['preparation'] ?? null;
    $result['ingredients'] = [];
    foreach ($query as $row) {
        $result['ingredients'][] = [
            'quantite' => $row['quantite'],
            'unite' => $row['unite'],
            'nom' => $row['nom'],
            'id' => $row['id']
        ];
    }
    $result['image'] = getImage("../images/Photos/", $result['titre']) ?? null;
    if (!empty($Neighborg)){
        $result['previous'] = $Neighborg['previous_id'] ?? null;
        $result['next'] = $Neighborg['next_id'] ?? null;
        $result['previous_name'] = $Neighborg['previous_name'] ?? null;
        $result['next_name'] = $Neighborg['next_name'] ?? null;
    }
}
header('Content-Type: application/json');

echo json_encode($result);
?>