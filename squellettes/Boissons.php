<?php
require_once __DIR__.'/../include/utils.php';
require __DIR__.'/../include/init.php';
$context = stream_context_create([
    'http' => [
        'timeout' => 10, // Timeout de 10 secondes
    ]
]);
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $jsonData = file_get_contents('http://elixirdelice.byethost16.com/donnee/BoissonInfo.php?search='.$search, false, $context);
} else {
    $jsonData = file_get_contents('http://elixirdelice.byethost16.com/donnee/BoissonInfo.php', false, $context);
}
$results = json_decode($jsonData, true);
if (isset($results['error'])) {
    echo $results['error'];
    exit;
}
foreach ($results as $boisson) {
    $id = $boisson['id'];
    $titre = $boisson['titre'];
    $isinbasket = checkInBasket($id);
    $isinfav = checkInFav($id);
    $isconnected = isset($_SESSION['user']);
    include  __DIR__.'/../squellettes/BoissonEtiquettes.php';
}
?>