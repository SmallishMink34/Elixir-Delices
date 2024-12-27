
<?php
    require_once __DIR__ . '/../include/utils.php';
    $identifiant = htmlspecialchars($id);
    $type = $type ?? 'boisson';
    $nom = htmlspecialchars($titre) ?? 'Titre inconnu';
    $imageSrc = getImage("../images/Photos/", $nom, $type);
    $isInBasket = $isinbasket ?? false;
    $isInFav = $isinfav ?? false;
    $isConnected = $isconnected ?? false;

?>
<div class="caseBoisson">
    <a href="?page=Boisson&drink=<?=$identifiant?>">
        <div>
            <img class="imgBoisson" src="<?= $imageSrc ?>" alt="">
        </div>
        <p><?= $nom ?></p>
    </a>
    <?php
        if ($type === 'boisson') {
            if ($isInBasket) {
                echo '<button class="btnPanier" disabled><img class="panierimg" src="images/icons/check.png" alt="Ajouter au Panier"></button>';
            } else {
                echo "<button id='panier$identifiant' class='btnPanier' onclick='addToBacket($identifiant)'> <img class='panierimg' src='images/icons/panier.png' alt='Ajouter au Panier'> </button>";
            }
            if ($isConnected) {
                if ($isInFav) {
                    echo "<label class='labelFab' for='favoris<?= $identifiant?>'><img class='etoile' src='images/icons/stars.png'  alt=''></label>";
                } else {
                    echo "<label class='labelFab' for='favoris<?= $identifiant?>'><img class='etoile' src='images/icons/stars_off.png'  alt=''></label>";
                }
            }
            echo '<input type="checkbox" name="" id="favoris'.$identifiant.'" onchange="changeFavoris('.$identifiant.')" class="favoris">';
        }


    ?>

</div>