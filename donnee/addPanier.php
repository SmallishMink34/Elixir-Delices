<?php
include_once __DIR__ . '/../include/db.php';


session_start(); // Assurez-vous de démarrer la session
header('Content-Type: application/json');
if (isset($_SESSION['user'])) {
    // TODO : ajout de la boisson dans le panier pour un utilisateur connecté
    echo json_encode(['success' => true, 'message' => 'Article ajouté au panier pour l\'utilisateur connecté']);
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
        setcookie('panier', json_encode($retrievedArray), time() + 3600, "/"); // Expire dans 1 heure
        
        echo json_encode(['success' => true, 'message' => 'Item added to the cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No item specified']);
    }
}
