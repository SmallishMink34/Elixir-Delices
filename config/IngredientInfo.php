<?php 
require '../include/db.php';
require '../include/utils.php';
// Connexion à la base de données
$pdo = getDatabaseConnection();
if (!$pdo) {
    echo "probleme de connexion";
    throw new Exception('Failed to connect to the database.');
}

$query = $_GET['ing'] ?? '';
if (empty($query)) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("WITH RECURSIVE Recursive_Ingredient_Categories AS (
  -- Étape de base : Commencez avec l'ingrédient enfant
  SELECT
    i.id AS ingredient_id,
    i.nom AS ingredient_nom,
    ric.categorie_id AS parent_id,
    ric.type_relation
  FROM
    ingredient i
  LEFT JOIN
    relation_ingredient_categorie ric ON i.id = ric.ingredient_id
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
    relation_ingredient_categorie ric ON ic.parent_id = ric.ingredient_id
  LEFT JOIN
    ingredient i ON ric.ingredient_id = i.id
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
LEFT JOIN ingredient i ON i.id = parent_id

UNION
SELECT DISTINCT i.nom as ingredient_nom, ingredient_id, NULL as parent, NULL as parent_nom
FROM relation_ingredient_categorie ric
JOIN ingredient i ON ric.ingredient_id = i.id
WHERE ric.ingredient_id NOT IN (
    SELECT ingredient_id
    FROM relation_ingredient_categorie
    WHERE type_relation = 'super'
)
AND ric.type_relation = 'sous' ;
");
$stmt->execute(['query' => $query]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

function buildTree(array $elements, $parent = null) {
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

$tree = buildTree($result);

header('Content-Type: application/json');

echo json_encode($tree, JSON_PRETTY_PRINT);
?>