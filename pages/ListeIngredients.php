<?php
require_once 'include/db.php';
require_once 'include/utils.php';
require __DIR__.'/../include/init.php';

$search = (isset($_GET['search'])) ? strtolower($_GET['search']) : '';

$listOfValidFilters = ['search'];

?>
<script>

    /*
    * Fonction qui permet de charger les Ingrédients (rechargement dynamique)
     */
    function loadIngredient(value = '') {
        if (value !== '') {
            fetch(`squellettes/Ingredients.php?search=${value}`) // requete sur la page Boissons.php
                .then(response => response.text())
                .then(data => {
                    document.getElementById('container_Boissons').innerHTML = data;
                });
        }else {
            fetch("squellettes/Ingredients.php") // requete sur la page Boissons.php
                .then(response => response.text())
                .then(data => {
                    document.getElementById('container_Boissons').innerHTML = data;
                });
        }
    }

    loadIngredient(<?= json_encode($search) ?>);
</script>

<div id="historique">
    <a href="?page=Home">Accueil</a> > <a href="?page=ListeBoissons">Liste des boissons</a>
</div>
<div class="filterCase">
    <?php foreach ($_GET as $key => $value) {
        if (in_array($key, $listOfValidFilters)) {
            include 'squellettes/filter.php';
        }

    } ?>
</div>
<div id="container_Boissons" class="container_Boissons">

</div>