<?php
// Configuration de la base de données
include("mdp.php");
include("Donnees.inc.php");

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Création de la base de données
    $pdo->exec("DROP DATABASE IF EXISTS $dbname");
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");

    // Création des tables
    $tables = [
        // Table Recette
        "CREATE TABLE IF NOT EXISTS Recette (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titre VARCHAR(255) NOT NULL,
            preparation TEXT NOT NULL
        )",

        // Table Ingrédient
        "CREATE TABLE IF NOT EXISTS Ingredient (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(255) UNIQUE NOT NULL
        )",

        // Table d'association entre Recette et Ingrédient
        "CREATE TABLE IF NOT EXISTS Recette_Ingredient (
            recette_id INT NOT NULL,
            ingredient_id INT NOT NULL,
            quantite VARCHAR(50),
            unite VARCHAR(50),
            PRIMARY KEY (recette_id, ingredient_id),
            FOREIGN KEY (recette_id) REFERENCES Recette(id) ON DELETE CASCADE,
            FOREIGN KEY (ingredient_id) REFERENCES Ingredient(id) ON DELETE CASCADE
        )",
        // Table des catégories
        "CREATE TABLE IF NOT EXISTS Categorie (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(255) UNIQUE NOT NULL 
        )",
        // Table des relations ingrédient-catégorie
        "CREATE TABLE IF NOT EXISTS Relation_Ingredient_Categorie (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ingredient_id INT NOT NULL,
            categorie_id INT NOT NULL,
            type_relation ENUM('super', 'sous') NOT NULL, # super ou sous
            FOREIGN KEY (ingredient_id) REFERENCES Ingredient(id) ON DELETE CASCADE,
            FOREIGN KEY (categorie_id) REFERENCES Categorie(id) ON DELETE CASCADE
        )"
    ];

    // Exécution des requêtes de création
    foreach ($tables as $query) {
        $pdo->exec($query);
    }

    // Préparer les requêtes
    $insertRecette = $pdo->prepare("INSERT INTO Recette (titre, preparation) VALUES (:titre, :preparation)");
    $insertIngredient = $pdo->prepare("INSERT IGNORE INTO Ingredient (nom) VALUES (:nom)");
    $insertRecetteIngredient = $pdo->prepare("
        INSERT INTO Recette_Ingredient (recette_id, ingredient_id, quantite, unite) 
        VALUES (:recette_id, :ingredient_id, :quantite, :unite)
    ");

    $insertCategorie = $pdo->prepare("INSERT IGNORE INTO Categorie (nom) VALUES (:nom)");
    $insertRelation = $pdo->prepare("
        INSERT INTO Relation_Ingredient_Categorie (ingredient_id, categorie_id, type_relation) 
        VALUES (:ingredient_id, :categorie_id, :type_relation)
    ");

    foreach ($Hierarchie as $ingredient => $relations) {
        // Insérer l'ingrédient principal
        $insertIngredient->execute([':nom' => $ingredient]);
        $ingredientId = $pdo->query("SELECT id FROM Ingredient WHERE nom = " . $pdo->quote($ingredient))->fetchColumn();

        // Traiter les sous-catégories
        if (isset($relations['sous-categorie'])) {
            foreach ($relations['sous-categorie'] as $sousCategorie) {
                $insertCategorie->execute([':nom' => $sousCategorie]);
                $categorieId = $pdo->query("SELECT id FROM Categorie WHERE nom = " . $pdo->quote($sousCategorie))->fetchColumn();

                // Ajouter la relation ingrédient → sous-catégorie
                $insertRelation->execute([
                    ':ingredient_id' => $ingredientId,
                    ':categorie_id' => $categorieId,
                    ':type_relation' => 'sous'
                ]);
            }
        }
        
        if (isset($relations['super-categorie'])) {
            foreach ($relations['super-categorie'] as $superCategorie) {
                $insertCategorie->execute([':nom' => $superCategorie]);
                $categorieId = $pdo->query("SELECT id FROM Categorie WHERE nom = " . $pdo->quote($superCategorie))->fetchColumn();

                // Ajouter la relation ingrédient → super-catégorie
                $insertRelation->execute([
                    ':ingredient_id' => $ingredientId,
                    ':categorie_id' => $categorieId,
                    ':type_relation' => 'super'
                ]);
            }
        }
    }
    // Lecture des recettes
    foreach ($Recettes as $recette) {
        // Insérer la recette
        $insertRecette->execute([
            ':titre' => $recette['titre'],
            ':preparation' => $recette['preparation']
        ]);
        $recetteId = $pdo->lastInsertId();

        // Traiter les ingrédients
        $ing_reci = $recette["ingredients"];


        /*
            (?:^|\|) : début de ligne ou un |
            \s* : espaces
            (\d+)? : chiffre (optionnel)
            \s* : espace
            (cl |g |kg |ml |l |lb )? : unité (optionnel)
            (.+?) : nom de l'ingrédient
            (?=\||$) : suivi d'un | ou de la fin de la ligne
        */
        preg_match_all('/(?:^|\|)\s*(\d+)?\s*(cl|g|kg|ml|l|càs|càc|oz|lb)?\s*(.+?)(?=\||$)/', $ing_reci, $matches, PREG_SET_ORDER);
        $index = 0;
        foreach ($matches as $match) {
            $quantite = $match[1] ?? null; // Quantité (ex: 10, 5)
            $unite = $match[2] ?? null;    // Unité (ex: cl, g)
            $nom = $recette["index"][$index];             // Nom de l'ingrédient

            // Insérer l'ingrédient
            $insertIngredient->execute([':nom' => $nom]);

            // Récupérer l'ID de l'ingrédient
            $ingredientId = $pdo->query("SELECT id FROM Ingredient WHERE nom = " . $pdo->quote($nom))->fetchColumn();

            try {
                // Associer la recette à l'ingrédient
                $insertRecetteIngredient->execute([
                    ':recette_id' => $recetteId,
                    ':ingredient_id' => $ingredientId,
                    ':quantite' => $quantite,
                    ':unite' => $unite
                ]);
            } catch (PDOException $e) {
                if ($e->getCode() != 23000) {
                    throw $e;
                }
            }
            $index++;
        }
    }

    echo "Base de données et données insérées avec succès !";
} catch (PDOException $e) {
    die("Erreur lors de la création ou l'insertion dans la base de données : " . $e->getMessage());
}
?>
