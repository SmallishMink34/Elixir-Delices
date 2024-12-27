<?php
require_once 'include/utils.php';
require __DIR__.'/../include/init.php';
session_write_close();
// Me permet de recuperer les cookies dans le fichier via le file_get_contents
$context = stream_context_create([
    'http' => [
        'header' => "Cookie: " . http_build_query($_COOKIE, '', '; ')
    ]
]);

$jsonData = file_get_contents('http://elixirdelice.byethost16.com/donnee/GetPanier.php', false, $context);
$results = json_decode($jsonData, true);
session_start();
if (isset($_SESSION['user']) && isset($_COOKIE['panier'])) {
    $message = "Il semble que vous ayez des boissons dans votre panier hors connexion. Voulez-vous les ajouter à votre compte?";
    $type = "warning";
    $title = "Panier en attente";
    $oui = "Ajouter au panier";
    $non = "Vider le panier hors connexion";
    $action = "./donnee/UpdatePanier.php";
    include __DIR__.'/../squellettes/alerts.php';
}

?>

<div id="panierBody" class="container_Boissons">
    <?php
        foreach ($results as $result) {
            $id = $result['id'];
            $titre = $result['titre'];
            $isinbasket = checkInBasket($id);
            include 'squellettes/BoissonEtiquettes.php';
        }
    ?>
</div>
