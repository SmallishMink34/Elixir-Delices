<?php

include_once __DIR__ . '/../include/db.php';

$conn = getDatabaseConnection();

function getSousCategoriesEtIngrédients($connexion, $racine): array
{
    // Requête pour récupérer les sous-catégories et ingrédients directement liés
    $stmt = $connexion->prepare("
        SELECT 
            i.id AS ingredient_id,
            i.nom AS ingredient_nom,
            ric.categorie_id AS parent_id
        FROM 
            Ingredient i
        JOIN 
            Relation_Ingredient_Categorie ric ON i.id = ric.ingredient_id
        WHERE 
            ric.type_relation = 'super' 
            AND ric.categorie_id = (SELECT id FROM Ingredient WHERE nom = :racine)
    ");
    $stmt->execute(['racine' => $racine]);

    // Si aucun résultat, retourner un tableau vide
    if ($stmt->rowCount() == 0) {
        return [];
    }

    // Initialisation du tableau de résultats
    $resultats = [];

    // Parcourir les résultats pour chaque sous-catégorie ou ingrédient
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Appel récursif pour récupérer les sous-éléments
        $sousElements = getSousCategoriesEtIngrédients($connexion, $row['ingredient_nom']);
        // Ajouter les données actuelles et les sous-éléments
        $resultats[] = [
            'ingredient_id' => $row['ingredient_id'],
            'ingredient_nom' => $row['ingredient_nom'],
            'sous_elements' => $sousElements
        ];
    }

    return $resultats;
}

// Exemple d'utilisation
$racine = "Aliment";
$resultat = getSousCategoriesEtIngrédients($conn, $racine);
// Affichage du résultat pour vérification
header('Content-Type: application/json');
echo json_encode($resultat, JSON_PRETTY_PRINT);
