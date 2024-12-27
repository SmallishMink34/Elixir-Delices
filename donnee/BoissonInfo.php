<?php 
include_once  __DIR__ . '/../include/db.php';;
include_once  __DIR__ .'/../include/utils.php';

header('Content-Type: application/json');


try {
    // Connexion à la base de données
    $pdo = getDatabaseConnection();

    $query = [];
    $results = [];

    $drinkIds = isset($_GET['drink']) ? explode(',', $_GET['drink']) : [];
    $drinkIds = array_filter($drinkIds, fn($id) => is_numeric($id)); // Filtrer les IDs non valides
    $search = $_GET['search'] ?? '';

    if (!empty($drinkIds)) {

        $placeholders = implode(',', array_map(fn($i) => ":id$i", array_keys($drinkIds)));
        $stmt = $pdo->prepare("
            SELECT r.id AS recette_id, r.titre, r.preparation, ri.quantite, ri.unite, i.nom, i.id AS ingredient_id
            FROM Recette r
            JOIN Recette_Ingredient ri ON ri.recette_id = r.id
            JOIN Ingredient i ON i.id = ri.ingredient_id
            WHERE r.id IN ($placeholders)
        ");

        $params = [];
        foreach ($drinkIds as $index => $id) {
            $params[":id$index"] = $id;
        }
        $stmt->execute($params);
    }  elseif (!empty($search)) {
        $stmt = $pdo->prepare("
            SELECT r.id AS recette_id, r.titre, r.preparation, ri.quantite, ri.unite, i.nom, i.id AS ingredient_id
            FROM Recette r
            JOIN Recette_Ingredient ri ON ri.recette_id = r.id
            JOIN Ingredient i ON i.id = ri.ingredient_id
            WHERE LOWER(r.titre) LIKE LOWER(:search)
        ");
        $stmt->execute(['search' => "%$search%"]);
    } else {
        $stmt = $pdo->prepare("
            SELECT r.id AS recette_id, r.titre, r.preparation, ri.quantite, ri.unite, i.nom, i.id AS ingredient_id
            FROM Recette r
            JOIN Recette_Ingredient ri ON ri.recette_id = r.id
            JOIN Ingredient i ON i.id = ri.ingredient_id
        ");
        $stmt->execute();
    }

    $query = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

            // Ajout des ingrédients
            $results[$recetteId]['ingredients'][] = [
                'quantite' => $row['quantite'],
                'unite' => $row['unite'],
                'nom' => $row['nom'],
                'id' => $row['ingredient_id']
            ];
        }

        // Gestion des voisins (previous/next)
        if (!isset($_GET['neighborg']) || $_GET['neighborg'] === 'true') {
            foreach ($results as $recetteId => &$result) {
                $stmtNeighborg = $pdo->prepare("
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
                    WHERE current_id = :id
                ");
                $stmtNeighborg->execute(['id' => $recetteId]);
                $neighborg = $stmtNeighborg->fetch(PDO::FETCH_ASSOC);

                if (!empty($neighborg)) {
                    $result['previous'] = $neighborg['previous_id'] ?? null;
                    $result['next'] = $neighborg['next_id'] ?? null;
                    $result['previous_name'] = $neighborg['previous_name'] ?? null;
                    $result['next_name'] = $neighborg['next_name'] ?? null;
                }
            }
        }
    } else {
        $results = ['error' => 'Aucun résultat trouvé'];
    }
    // Envoi de la réponse JSON
    echo json_encode($results);
    error_log('Fin du traitement');
} catch (Exception $e) {
    // Gestion des erreurs
    error_log('Erreur : ' . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}