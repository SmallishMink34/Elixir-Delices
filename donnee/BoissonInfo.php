<?php 
error_log('Début du traitement');
include_once  __DIR__ . '/../include/db.php';;
include_once  __DIR__ .'/../include/utils.php';
// Connexion à la base de données
$pdo = getDatabaseConnection();
if (!$pdo) {
    echo "probleme de connexion";
    throw new Exception('Failed to connect to the database.');
}

$getValue = $_GET['drink'] ?? '';
if (!empty($getValue)) {
    $drinkIds = explode(',', $getValue);
    $drinkIds = array_filter($drinkIds, fn($id) => is_numeric($id)); // Evite les injections SQL

    if(empty($drinkIds)){ // Cas ou il n'y a pas d'id
        echo json_encode([]);
        exit;
    }

    // Me permet de gerer les id en parametre
    $placeholders = implode(',', array_map(fn($i) => ":id$i", array_keys($drinkIds)));

    $stmt = $pdo->prepare("SELECT r.id as recette_id, r.titre, r.preparation, ri.quantite, ri.unite, i.nom, i.id as ingredient_id FROM Recette r join Recette_Ingredient ri on ri.recette_id = r.id join Ingredient i on i.id = ri.ingredient_id  WHERE r.id IN ($placeholders)");

    $params = [];
    foreach ($drinkIds as $index => $id) {
        $params[":id$index"] = $id;
    }
    $stmt->execute($params);
    $query = $stmt->fetchAll(PDO::FETCH_ASSOC);
}else{
    $stmt = $pdo->prepare("SELECT r.id as recette_id, r.titre, r.preparation, ri.quantite, ri.unite, i.nom, i.id as ingredient_id FROM Recette r join Recette_Ingredient ri on ri.recette_id = r.id join Ingredient i on i.id = ri.ingredient_id");
    $stmt->execute();
    $query = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$result = [];
$results = [];

if (!empty($query)) {
    foreach ($query as $row) {
        $recetteId = $row['recette_id'];

        if (!isset($results[$recetteId])) {
            $results[$recetteId] = [
                'id' => $row['recette_id'],
                'titre' => $row['titre'] ?? null,
                'titreFormat' => formatTitre($row['titre']),
                'preparation' => $row['preparation'] ?? null,
                'ingredients' => [],
                'image' => getImage("../images/Photos/", $row['titre']) ?? null,
            ];
        }
        // Ajoute chaque ingrédient
        $results[$recetteId]['ingredients'][] = [
            'quantite' => $row['quantite'],
            'unite' => $row['unite'],
            'nom' => $row['nom'],
            'id' => $row['ingredient_id']
        ];
    }
}
if ((isset($_GET['neighborg']) && $_GET['neighborg'] == 'true') || (!isset($_GET['neighborg']))) {  
    foreach ($results as $recetteId => &$result) {
        $requeteNeighborg = $pdo->prepare("
            SELECT * FROM (
                SELECT 
                    LAG(id) OVER (ORDER BY id) AS previous_id,
                    LAG(titre) OVER (ORDER BY id) AS previous_name,
                    id AS current_id,
                    titre AS current_name,
                    LEAD(id) OVER (ORDER BY id) AS next_id,
                    LEAD(titre) OVER (ORDER BY id) AS next_name
                FROM Recette
            ) subquery
            WHERE current_id = :query
        ");
        $requeteNeighborg->execute(['query' => $recetteId]);
        $Neighborg = $requeteNeighborg->fetch(PDO::FETCH_ASSOC);

        if (!empty($Neighborg)) {
            $result['previous'] = $Neighborg['previous_id'] ?? null;
            $result['next'] = $Neighborg['next_id'] ?? null;
            $result['previous_name'] = $Neighborg['previous_name'] ?? null;
            $result['next_name'] = $Neighborg['next_name'] ?? null;
        }
    }
}
header('Content-Type: application/json');

echo json_encode($results);
error_log('Fin du traitement');
