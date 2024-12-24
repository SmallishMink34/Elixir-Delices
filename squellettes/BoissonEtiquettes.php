
<?php
    require_once __DIR__ . '/../include/utils.php';
    $identifiant = htmlspecialchars($id);
    $nom = htmlspecialchars($titre) ?? 'Titre inconnu';
    $imageSrc = getImage("../images/Photos/", $nom);
    $isInBasket = $isinbasket ?? false;
    $isInFav = $isinfav ?? false;
?>
<div class="caseBoisson">
    <a href="?page=Boisson&drink=<?=$identifiant?>">
        <div>
            <img class="imgBoisson" src="<?= $imageSrc ?>" alt="">
        </div>
        <p><?= $nom ?></p>
    </a>
    <?php if ($isInBasket){
        echo '<button class="btnPanier" disabled><img class="panierimg" src="images/icons/check.png" alt="Ajouter au Panier"></button>';
        } else {
        echo "<button id='panier$identifiant' class='btnPanier' onclick='addToBacket($identifiant)'> <img class='panierimg' src='images/icons/panier.png' alt='Ajouter au Panier'> </button>";
        }
    if ($isInFav){
        echo "<label class='labelFab' for='favoris<?= $identifiant?>'><img class='etoile' src='images/icons/stars.png'  alt=''></label>";
    } else {
        echo "<label class='labelFab' for='favoris<?= $identifiant?>'><img class='etoile' src='images/icons/stars_off.png'  alt=''></label>";
    }

    ?>
    <input type="checkbox" name="" id="favoris<?= $identifiant?>" onchange="changeFavoris('favoris<?= $identifiant?>')" class="favoris">
</div>