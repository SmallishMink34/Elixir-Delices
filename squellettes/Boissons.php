<?php
require_once __DIR__.'/../include/utils.php';
$context = stream_context_create([
    'http' => [
        'timeout' => 10, // Timeout de 10 secondes
    ]
]);
$jsonData = file_get_contents('http://elixirdelice.byethost16.com/donnee/BoissonInfo.php', false, $context);
$results = json_decode($jsonData, true);
foreach ($results as $boisson) {
    $id = $boisson['id'];
    $titre = $boisson['titre'];
    $isinbasket = checkInBasket($id);
    include  __DIR__.'/../squellettes/BoissonEtiquettes.php';
}
?>