<?php
include_once __DIR__ . '/../include/db.php';


session_start(); // Assurez-vous de démarrer la session
header('Content-Type: application/json');
if (isset($_SESSION['user'])) {
    if (isset($_GET['add']) && !empty($_GET['add'])){
        $conn = getDatabaseConnection();
        $stmt = $conn->prepare("SELECT quantite FROM PANIER WHERE id_personne = :id_personne AND id_recette = :id_boisson");
        $stmt->execute(['id_personne' => $_SESSION['user'], 'id_boisson' => $_GET['add']]);
        $quantite = $stmt->fetch();
        $quantiteAdd = isset($_GET['quantite']) ? (int)$_GET['quantite'] : 1;
        $quantFinal = ($quantite['quantite'] ?? 0) + $quantiteAdd;
        $panier = $stmt->fetchAll();
        if ($quantite == null) {
            $stmt = $conn->prepare("INSERT INTO PANIER (id_personne, id_recette, quantite) VALUES (:id_personne, :id_boisson,  :quantite)");
            $stmt->execute(['id_personne' => $_SESSION['user'], 'id_boisson' => $_GET['add'], 'quantite' => $quantFinal]);

        } else {
            $stmt = $conn->prepare("UPDATE PANIER SET quantite = :quantite WHERE id_personne = :id_personne AND id_recette = :id_boisson");
            $stmt->execute(['id_personne' => $_SESSION['user'], 'id_boisson' => $_GET['add'], 'quantite' => $quantFinal]);
        }

        $getPanier = $conn->prepare("SELECT * FROM PANIER WHERE id_personne = :id_personne");
        $getPanier->execute(['id_personne' => $_SESSION['user']]);
        $panierFInal = $getPanier->fetchAll();
        $_SESSION['panier'] = [];
        foreach ($panierFInal as $key) {
            $_SESSION['panier'][] = $key['id_recette'];
        }

        echo json_encode(['success' => true, 'message' => 'Article ajouté au panier pour l\'utilisateur connecté']);
    }
} else {
    if (isset($_GET['add']) && !empty($_GET['add'])) {
        $itemToAdd = htmlspecialchars($_GET['add']); // Sécurise l'entrée
        if (isset($_COOKIE['panier'])) {
            // Récupérer et convertir le cookie en tableau
            $retrievedArray = json_decode($_COOKIE['panier'], true);
            
            // Vérifiez que la conversion JSON a réussi
            if (is_array($retrievedArray)) {
                $retrievedArray[] = $itemToAdd;
            } else {
                $retrievedArray = [$itemToAdd];
            }
        } else {
            $retrievedArray = [$itemToAdd];
        }
        setcookie('panier', json_encode($retrievedArray) , time() + 3600*24*30, '/');
        
        echo json_encode(['success' => true, 'message' => 'Item added to the cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No item specified']);
    }
}
