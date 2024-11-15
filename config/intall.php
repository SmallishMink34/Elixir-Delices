<?php
// Configuration de la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "elixir_delices";

// Connexion à MySQL
$conn = new mysqli($servername, $username, $password);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Créer la base de données
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $conn->error;
}

// Sélectionner la base de données
$conn->select_db($dbname);

// Créer les tables nécessaires RECETTE
$sql = "CREATE TABLE IF NOT EXISTS Recettes (
    id_Recette INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    preparation TEXT,
    ingredients TEXT,
    foreign key (id_Recette) references Ingredients-Recettes(id_recette)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table recette created successfully\n";
} else {
    echo "Error creating table: " . $conn->error;
}

// Créer les tables nécessaires RECETTE
$sql2 = "CREATE TABLE IF NOT EXISTS Ingredients_Recettes (
    id_ING_REC INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Ing_NOM VARCHAR(255) NOT NULL,
    id_Recette INT(6) NOT NULL
)";

if ($conn->query($sql2) === TRUE) {
    echo "Table recette created successfully\n";
} else {
    echo "Error creating table: " . $conn->error;
}

$sql3 = "CREATE TABLE IF NOT EXISTS SuperCategorie (
    Ing_Nom VARCHAR(255) NOT NULL PRIMARY KEY,
    SuperCat VARCHAR(255),
    FOREIGN KEY (Ing_Nom) REFERENCES `Ingredients_Recettes`(Ing_Nom),
    FOREIGN KEY (SuperCat) REFERENCES SuperCategorie(Ing_Nom)
)";

if ($conn->query($sql3) === TRUE) {
    echo "Table SuperCategorie created successfully\n";
} else {
    echo "Error creating table: " . $conn->error;
}

// Créer la table nécessaire SousCategorie
$sql4 = "CREATE TABLE IF NOT EXISTS SousCategorie (
    Ing_Nom VARCHAR(255) NOT NULL PRIMARY KEY,
    SousCat VARCHAR(255),
    FOREIGN KEY (Ing_Nom) REFERENCES `Ingredients_Recettes`(Ing_Nom),
    FOREIGN KEY (SousCat) REFERENCES SuperCategorie(Ing_Nom)
)";

if ($conn->query($sql4) === TRUE) {
    echo "Table SousCategorie created successfully\n";
} else {
    echo "Error creating table: " . $conn->error;
}



// Inclure le fichier Donnees.inc.php
include 'Donnees.inc.php';

// Insérer les données dans la table produits
foreach ($Recettes as $recette) {
    $nom = $conn->real_escape_string($recette['titre']);
    $ingredient = $conn->real_escape_string($recette['ingredients']);
    $preparation = $conn->real_escape_string($recette['preparation']);

    $sql = "INSERT INTO Recettes (nom, preparation, ingredient) VALUES ('$nom', '$ingredient', '$preparation')";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully\n";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

foreach ($hier as $Hierarchie){
    $nom = $conn->real_escape_string($hier);
    $super = $conn->real_escape_string($Hierarchie['super-categorie']);
    $
}

// Fermer la connexion
$conn->close();
?>