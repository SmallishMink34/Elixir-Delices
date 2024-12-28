<?php 
include_once  __DIR__ . '/../include/db.php';;
include_once  __DIR__ .'/../include/utils.php';

header('Content-Type: application/json');


try {
    // Connexion à la base de données
    $pdo = getDatabaseConnection();

    $query = [];
    $resultsAlt = [];

    $drinkIds = isset($_GET['drink']) ? explode(',', $_GET['drink']) : [];
    $drinkIds = array_filter($drinkIds, fn($id) => is_numeric($id)); // Filtrer les IDs non valides
    $search = $_GET['search'] ?? '';
    $filter = $_GET['filter'] ?? '';
    $blackList = [];
    if ($filter != '') {
        $blackList = file_get_contents('http://elixirdelice.byethost16.com/donnee/getBlackList.php?ing='.$filter);
        $blackList = json_decode($blackList, false);
    }




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
    }  else {
        $stmt = $pdo->prepare("
            SELECT r.id AS recette_id, r.titre, r.preparation, ri.quantite, ri.unite, i.nom, i.id AS ingredient_id
            FROM Recette r
            JOIN Recette_Ingredient ri ON ri.recette_id = r.id
            JOIN Ingredient i ON i.id = ri.ingredient_id
            WHERE LOWER(r.titre) LIKE LOWER(:search);
        ");
        $stmt->execute(['search' => "%$search%"]);
    }
    $query = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($query)) {

        foreach ($query as $row) {
            $recetteId = $row['recette_id'];
            $list_ingId = [];
            if (!isset($resultsAlt[$recetteId])) {
                $resultsAlt[$recetteId] = [
                    'id' => $row['recette_id'],
                    'titre' => $row['titre'] ?? null,
                    'titreFormat' => formatTitre($row['titre']),
                    'preparation' => $row['preparation'] ?? null,
                    'ingredients' => [],
                    'image' => getImage("../images/Photos/", $row['titre']) ?? null,
                ];
            }

            // Ajout des ingrédients
            $resultsAlt[$recetteId]['ingredients'][] = [
                'quantite' => $row['quantite'],
                'unite' => $row['unite'],
                'nom' => $row['nom'],
                'id' => $row['ingredient_id']
            ];
        }
        if (!empty($filter)) {
            $resultsAlt = array_filter($resultsAlt, function ($recette) use ($blackList) {
                $ingredients = array_column($recette['ingredients'], 'id');
                return !array_intersect($ingredients, $blackList);
            });
        }


        // Gestion des voisins (previous/next)
        if (!isset($_GET['neighborg']) || $_GET['neighborg'] === 'true') {
            foreach ($resultsAlt as $recetteId => &$result) {
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
        $resultsAlt = ['error' => 'Aucun résultat trouvé'];
    }
    // Envoi de la réponse JSON
    echo json_encode($resultsAlt);
    error_log('Fin du traitement');
} catch (Exception $e) {
    // Gestion des erreurs
    error_log('Erreur : ' . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}