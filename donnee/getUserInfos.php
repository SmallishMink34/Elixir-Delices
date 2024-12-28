<?php
require_once __DIR__.'/../include/init.php';
require_once __DIR__.'/../include/db.php';


$conn = getDatabaseConnection();
$stmt = $conn->prepare("SELECT * FROM PERSONNE WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $_SESSION['user']]);
$result = $stmt->fetch();

$fav = $conn->prepare("SELECT * FROM FAVORIS WHERE id_personne = :id_personne");
$fav->execute(['id_personne' => $_SESSION['user']]);

$panier = $conn->prepare("SELECT id_recette FROM PANIER WHERE id_personne = :id_personne");
$panier->execute(['id_personne' => $_SESSION['user']]);


$_SESSION['email'] = $result['email'];
$_SESSION['nom'] = $result['nom'];
$_SESSION['prenom'] = $result['prenom'];
$panier = $panier->fetchAll();
$_SESSION['panier'] = [];
$_SESSION['favoris'] = [];
$_SESSION['favoris'] = $fav->fetchAll();
foreach ($panier as $key) {
    $_SESSION['panier'][] = $key['id_recette'];
}