<?php
/**
 * Récupère les catégories d'ingrédients d'une liste d'ingrédients
 */
include_once __DIR__ . '/../include/db.php';
header('Content-Type: application/json');

if (isset($_GET['ing'])) {
    $ingList = $_GET['ing'];
    $ingList = trim($ingList, '[]');

    $ingList = explode(',', $ingList);

    $ingList = array_filter($ingList, 'is_numeric');

    if (empty($ingList)) {
        echo json_encode(['error' => 'Invalid ingredient list']);
        exit;
    }


    $placeholders = implode(',', array_fill(0, count($ingList), '?'));

    $query = "
        WITH RECURSIVE Recursive_Ingredient_Categories AS (
            SELECT
                i.id AS ingredient_id,
                i.nom AS ingredient_nom,
                ric.categorie_id AS parent_id,
                ric.type_relation
            FROM
                Ingredient i
            LEFT JOIN
                Relation_Ingredient_Categorie ric ON i.id = ric.ingredient_id
            WHERE
                i.id IN ($placeholders) AND ric.type_relation = 'sous'
            UNION ALL
            SELECT
                i.id AS ingredient_id,
                i.nom AS ingredient_nom,
                ric.categorie_id AS parent_id,
                ric.type_relation
            FROM
                Recursive_Ingredient_Categories ic
            LEFT JOIN
                Relation_Ingredient_Categorie ric ON ic.parent_id = ric.ingredient_id
            LEFT JOIN
                Ingredient i ON ric.ingredient_id = i.id
            WHERE
                ric.type_relation = 'sous'
        )
        SELECT DISTINCT
            ingredient_id
        FROM
            Recursive_Ingredient_Categories ric
        LEFT JOIN Ingredient i ON i.id = parent_id
    ";

    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare($query);
        $stmt->execute($ingList);

        $results = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        echo json_encode($results);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Missing ingredient parameter']);
}