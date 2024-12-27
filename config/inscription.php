<?php
include "../config/mdp.php";
include "../include/db.php";

if (!isset($_POST['nom'])){
    header('Location: ../pages/Signup.php');
    exit();
}

$nom = htmlspecialchars(trim($_POST['nom']), ENT_QUOTES, 'UTF-8'); // Sécurisation des données reçues
if (empty($nom) || strlen($nom) > 50 || !preg_match("/^[a-zA-Z-' ]*$/", $nom)) {
    header('Location: ../pages/Signup.php?error=1'); // On ne précise pas quel champ est incorrect
    exit();
}

$prenom = htmlspecialchars(trim($_POST['prenom']), ENT_QUOTES, 'UTF-8'); // Sécurisation des données reçues
if (empty($prenom) || strlen($prenom) > 50 || !preg_match("/^[a-zA-Z-' ]*$/", $prenom)) {
    header('Location: ../pages/Signup.php?error=2');
    exit();
}

$email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8'); // Sécurisation des données reçues
if (empty($email) || strlen($email) > 50 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../pages/Signup.php?error=3');
    exit();
}

$mot_de_passe = htmlspecialchars(trim($_POST['mot_de_passe']), ENT_QUOTES, 'UTF-8'); // Sécurisation des données reçues
if (empty($mot_de_passe) || strlen($mot_de_passe) < 8 || !preg_match("/[A-Z]/", $mot_de_passe) || !preg_match("/[0-9]/", $mot_de_passe)) {
    header('Location: ../pages/Signup.php?error=4');
    exit();
}

$conn = getDatabaseConnection();
$stmt = $conn->prepare("INSERT INTO PERSONNE (nom, prenom, email, mdp) VALUES (:nom, :prenom, :email, :mot_de_passe)");
$stmt->bindParam(':nom', $nom);
$stmt->bindParam(':prenom', $prenom);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':mot_de_passe', password_hash($mot_de_passe, $hash));

if ($stmt->execute()) {
    header('Location: ../pages/Login.php?success=1');
    exit();
} else {
    header('Location: ../pages/Signup.php?error=5');
    exit();
}


$conn->close();
?>
