<?php
require_once 'include/db.php';
require_once 'include/utils.php';
require __DIR__.'/../include/init.php';

$search = (isset($_GET['search'])) ? strtolower($_GET['search']) : '';

$listOfValidFilters = ['search'];



?>
<script>
    /*
    * Fonction qui permet de changer l'état d'un favoris
    */
    function changeFavoris(value) {
        const favoris = document.getElementById(value);
        const id = value;
        if (favoris.checked) {
            fetch(`donnee/addFavoris.php?add=${id}`)
                .then(response => response.json())
        }else{
            fetch(`donnee/addFavoris.php?remove=${id}`)
                .then(response => response.json())
                .then(data => {
                });
        }
    }

    /*
    * Fonction qui permet d'ajouter une boisson au panier
     */
    function addToBacket(value) {
        console.log(value);
        fetch(`donnee/addPanier.php?add=${encodeURIComponent(value)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log(data);
                    loadBoisson();
                } else if (data.message) {
                    // Affiche le message d'erreur spécifique du serveur
                } else {
                    // Message générique en cas d'erreur inconnue
                }
            })
            .catch(error => {
                // Gestion des erreurs réseau ou des exceptions
                console.error('Erreur lors de l\'ajout au panier:', error);
                alert('Impossible d\'ajouter l\'article au panier. Veuillez réessayer plus tard.');
            });
    }

    /*
    * Fonction qui permet de charger les boissons (rechargement dynamique)
     */
    function loadBoisson(value = '') {
        if (value !== '') {
            fetch(`squellettes/Boissons.php?search=${value}`) // requete sur la page Boissons.php
                .then(response => response.text())
                .then(data => {
                    document.getElementById('container_Boissons').innerHTML = data;
                });

        }else {
        fetch("squellettes/Boissons.php") // requete sur la page Boissons.php
            .then(response => response.text())
            .then(data => {
                document.getElementById('container_Boissons').innerHTML = data;
            });
        }
    }

    loadBoisson(<?= json_encode($search) ?>);
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