<?php
include_once __DIR__ . '/../include/db.php';
include_once __DIR__ . '/../include/utils.php';


session_start();

if (!isset($_SESSION['user']) && !isset($_COOKIE['panier']) && !isset($_POST['addPanier'])) {
    exit();
}


if ($_POST['addPanier'] == 'true') {
    $panier = getCookieData('panier');
    foreach ($_POST as $key => $value) {
        if (is_numeric($key)){
            $jsonData = file_get_contents('http://elixirdelice.byethost16.com/donnee/addPanier.php?add='.urlencode($key));
        }
    }
    viderCookie('panier');
} else if ($_POST['addPanier'] == 'false'){
    viderCookie('panier');
}
header('Location: http://elixirdelice.byethost16.com/index.php?page=Panier');