<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$username = 'nouvel_utilisateur'; 
$password = 'mot_de_passe';     
$dbname = 'utilisateurDB';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$dbname = 'utilisateurDB';


$conn = new mysqli($host, $username, $password);


if ($conn->connect_error) {
    die("Échec de la connexion à la base de données: " . $conn->connect_error);
}


$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Base de données '$dbname' est prête ou déjà existante.<br>";
} else {
    echo "Erreur lors de la création de la base de données: " . $conn->error . "<br>";
}


$conn->select_db($dbname);


$sql = "CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'utilisateurs' prête ou déjà existante.<br>";
} else {
    echo "Erreur lors de la création de la table: " . $conn->error . "<br>";
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Hacher mdp
    $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nom, $email, $mot_de_passe_hache);
    if ($stmt->execute()) {
        echo "Inscription réussie !<br>";
    } else {
        echo "Erreur : " . $stmt->error . "<br>";
    }


    $stmt->close();
}


$conn->close();
?>
