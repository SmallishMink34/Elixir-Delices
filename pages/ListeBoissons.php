<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'include/db.php';
require_once 'include/utils.php';
$search = (isset($_GET['search'])) ? strtolower($_GET['search']) : '';

$listOfValidFilters = ['search'];



?>
<script>
    function changeFavoris(value) {
        const favoris = document.getElementById(value);
        const id = value.replace('favoris', '');
        if (favoris.checked) {
            fetch(`donnee/removeFavoris.php?add=${id}`)
                .then(response => response.json())
                .then(data => {
                });
            return;
        }else{
            fetch(`donnee/removeFavoris.php?remove=${id}`)
                .then(response => response.json())
                .then(data => {
                });
            return;
        }
    }

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

    function loadBoisson(){
        fetch("squellettes/Boissons.php")
            .then(response => response.text())
            .then(data => {
                document.getElementById('container_Boissons').innerHTML = data;
            });
    }

    loadBoisson();
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