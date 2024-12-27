<?php 
include_once __DIR__ . '/../include/db.php';
include_once __DIR__ . '/../include/utils.php';

header('Content-Type: application/json');
// Connexion à la base de données
$pdo = getDatabaseConnection();

$result = array();
if (!isset($_GET['search']) && isset($_GET['ing'])) {
    $query = $_GET['ing'];
    /* Requete SQL Recursive afin de recuperer les super-catégories d'un ingrédient */
    $stmt = $pdo->prepare("WITH RECURSIVE Recursive_Ingredient_Categories AS (
      -- Étape de base : Commencez avec l'ingrédient enfant
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
        i.id = :query and ric.type_relation = 'super' -- Ingrédient de départ
      UNION ALL
      -- Étape récursive : Trouvez les catégories parentes et ajoutez au chemin
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
        ric.type_relation = 'super' -- Suivez uniquement les relations 'super'
    )
    -- Résultats finaux
    SELECT DISTINCT
      ingredient_nom,
      ingredient_id,
      parent_id AS parent,
      i.nom AS parent_nom
    FROM
      Recursive_Ingredient_Categories ric
    LEFT JOIN Ingredient i ON i.id = parent_id
    
    UNION
    SELECT DISTINCT i.nom as ingredient_nom, ingredient_id, NULL as parent, NULL as parent_nom
    FROM Relation_Ingredient_Categorie ric
    JOIN Ingredient i ON ric.ingredient_id = i.id
    WHERE ric.ingredient_id NOT IN (
        SELECT ingredient_id
        FROM Relation_Ingredient_Categorie
        WHERE type_relation = 'super'
    )
    AND ric.type_relation = 'sous' ;
    ");
    $stmt->execute(['query' => $query]);
    $arbre = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT ri.recette_id, r.titre FROM Recette_Ingredient ri join Recette r on r.id = ri.recette_id WHERE ri.ingredient_id = :id;");
    $ingredient = $pdo->prepare("SELECT nom, id FROM Ingredient WHERE id = :id");
    $sousCat = $pdo->prepare("SELECT i.nom, i.id FROM Ingredient i join Relation_Ingredient_Categorie on i.id = ingredient_id WHERE categorie_id = :id and type_relation = 'super'");
    $superCat = $pdo->prepare("SELECT i.nom, i.id FROM Ingredient i join Relation_Ingredient_Categorie on i.id = ingredient_id WHERE categorie_id = :id and type_relation = 'sous'");

    function buildTree(array $elements, $parent = null): array
    {
      $branch = [];

      foreach ($elements as $element) {
          if ($element['parent_nom'] === $parent) {
              $children = buildTree($elements, $element['ingredient_nom']);
              if ($children) {
                  $element['children'] = $children;
              }
              $branch[] = $element;
          }
      }

      return $branch;
    }

    $tree = buildTree($arbre);
    $stmt2->execute(['id' => $query]);
    $result['hierarchy'] = $tree;
    $result['recettes'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    $ingredient->execute(['id' => $query]);
    $ingredients = $ingredient->fetchAll(PDO::FETCH_ASSOC);
    $result['ingredient'] = $ingredients[0];

    $sousCat->execute(['id' => $query]);
    $result['sousCat'] = $sousCat->fetchAll(PDO::FETCH_ASSOC);

    $superCat->execute(['id' => $query]);
    $result['superCat'] = $superCat->fetchAll(PDO::FETCH_ASSOC);

}else{
    $search = $_GET['search'] ?? '';

    $stmt2 = $pdo->prepare("SELECT nom, id FROM Ingredient WHERE lower(nom) LIKE lower(:search)");
    $stmt2->execute(['search' => "%$search%"]);
    $query = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $result = $query;
}



if (empty($result)) {
    echo json_encode(['error' => 'Aucun résultat trouvé']);
    exit;
}



echo json_encode($result, JSON_PRETTY_PRINT);
?>