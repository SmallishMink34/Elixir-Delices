<?php
require_once 'include/utils.php';
// Me permet de recuperer les cookies dans le fichier via le file_get_contents
$context = stream_context_create([
    'http' => [
        'header' => "Cookie: " . http_build_query($_COOKIE, '', '; ')
    ]
]);

$jsonData = file_get_contents('http://elixirdelice.byethost16.com/donnee/GetPanier.php', false, $context);
$results = json_decode($jsonData, true);
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
