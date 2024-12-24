<?php
include_once __DIR__ . '/../include/db.php';



if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'You must be logged in to add or remove a favoris']);
    exit;
}

if(isset($_GET['add'])){
    $id = $_GET['add'];
    if (!is_numeric($id)) {
        echo json_encode(['error' => 'id must be a number']);
        exit;
    }

    $userid = $_SESSION['id'];
    $pdo = getDatabaseConnection();


    $stmt = $pdo->prepare("SELECT * FROM Favoris WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $id , 'user_id' => $userid]);
    $favoris = $stmt->fetch();
    if (!$favoris) {
        $stmt = $pdo->prepare("INSERT INTO Favoris (id, personne_id) VALUES (:id, :user_id)");
        $stmt->execute(['id' => $id, 'user_id' => $userid]);
        echo json_encode(['success' => 'Favoris added']);
    } else {
        echo json_encode(['message' => 'Favoris already exists']);
    }
} 