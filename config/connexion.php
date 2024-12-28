<?php
include "../config/mdp.php";
include "../include/db.php";
require __DIR__.'/../include/init.php';

if (isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}

if (!isset($_POST['email'])){
    header('Location: ../pages/Login.php');
    exit();
}


$email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8'); // Sécurisation des données reçues
if (empty($email) || strlen($email) > 50 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../pages/Login.php?error=1'); // On ne précise pas quel champ est incorrect
    exit();
}

$mot_de_passe = htmlspecialchars(trim($_POST['mot_de_passe']), ENT_QUOTES, 'UTF-8'); // Sécurisation des données reçues
if (empty($mot_de_passe) || strlen($mot_de_passe) < 8) {
    header('Location: ../pages/Login.php?error=1'); // On ne précise pas quel champ est incorrect
    exit(); // On ne précise pas quel champ est incorrect
}

$conn = getDatabaseConnection();
$stmt = $conn->prepare("SELECT id, mdp FROM PERSONNE WHERE email = :email LIMIT 1");
$result = [];
if ($stmt->execute(['email' => $email])) {
    $result = $stmt->fetch();
    if (!$result) {
        header('Location: ../pages/Login.php?error=1'); // On ne précise pas quel champ est incorrect
        exit(); // On ne précise pas quel champ est incorrect
    }else{

        if (!password_verify($mot_de_passe, $result['mdp'])) {
            header('Location: ../pages/Login.php?error=1'); // On ne précise pas quel champ est incorrect
            exit(); // On ne précise pas quel champ est incorrect
        }
    }
}

session_start();
$_SESSION['user'] = $result['id'];
include "../donnee/getUserInfos.php";



header('Location: ../index.php');

$conn = null;
?>
