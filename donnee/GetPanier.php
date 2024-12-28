<?php
session_start();
include "../donnee/getUserInfos.php";

header('Content-Type: application/json');
function getCookieData($cookieName) {
    return isset($_COOKIE[$cookieName]) ? json_decode($_COOKIE[$cookieName], false) : null;
}

function GetPanierFromDB()
{
    return $_SESSION['panier'] ?? [];
}

function fetchDrinkInfo($drinkKey) {
    $url = "http://elixirdelice.byethost16.com/donnee/BoissonInfo.php?drink=" . urlencode($drinkKey);
    $response = file_get_contents($url);

    if ($response === false) {
        throw new Exception("Erreur lors de la récupération des informations pour la boisson : $drinkKey");
    }
    return json_decode($response, true);
}

function enrichPanierData($panier): array
{
    // Ajoute les informations des boissons au tableau du panier
    $result = [];
    foreach ($panier as $key) {
        try {
            $drinkInfo = fetchDrinkInfo($key)[$key];
            $key = $drinkInfo;
            $result[] = $key;
        } catch (Exception $e) {
            // Gestion des erreurs pour chaque boisson
            error_log($e->getMessage());
            $key->info = null; // Ajoute une valeur nulle si l'information n'a pas pu être récupérée
        }
    }

    return $result;
}
if (isset($_SESSION['user'])) {
    $panier = GetPanierFromDB();
} else {
    $panier = getCookieData('panier');
}
if ($panier !== null) {
    $panier = enrichPanierData($panier);
    echo json_encode($panier);
} else {
    echo json_encode(['error' => 'Cookie non défini.']);
}