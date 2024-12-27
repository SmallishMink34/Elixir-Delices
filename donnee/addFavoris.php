<?php
include_once __DIR__ . '/../include/db.php';



if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'You must be logged in to add or remove a favoris']);
    exit;
}

if(isset($_GET['add']) || isset($_GET['del'])) {
    $id = $_GET['add'];
    if (!is_numeric($id)) {
        echo json_encode(['error' => 'id must be a number']);
        exit;
    }

    $userid = $_SESSION['id'];
    $pdo = getDatabaseConnection();


    $stmt = $pdo->prepare("SELECT * FROM Favoris WHERE id_recette = :id AND id_personne = :user_id");
    $stmt->execute(['id' => $id, 'user_id' => $userid]);
    $favoris = $stmt->fetch();

    if (isset($_GET['add'])) {
        if (!$favoris) {
            $stmt = $pdo->prepare("INSERT INTO Favoris (id_recette, id_personne) VALUES (:id, :user_id)");
            $stmt->execute(['id' => $id, 'user_id' => $userid]);

            echo json_encode(['success' => 'Favoris added']);
        } else {
            echo json_encode(['message' => 'Favoris already exists']);
        }
    } else {
        if ($favoris) {
            $stmt = $pdo->prepare("DELETE FROM Favoris WHERE id_recette = :id AND id_personne = :user_id");
            $stmt->execute(['id' => $id, 'user_id' => $userid]);

            echo json_encode(['success' => 'Favoris removed']);
        } else {
            echo json_encode(['message' => 'Favoris does not exists']);
        }
    }


    $fav = $pdo->prepare("SELECT * FROM Favoris WHERE id_personne = :id_personne");
    $fav->execute(['id_personne' => $userid]);
    $fav = $fav->fetchAll();
    foreach ($fav as $key) {
        $_SESSION['favoris'][] = $key['id_recette'];
    }
}