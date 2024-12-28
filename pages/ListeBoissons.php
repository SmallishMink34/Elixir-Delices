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


    function toggleChildren(checkbox) {
        const parentDiv = checkbox.closest(".lev" + checkbox.className.match(/checkbox-level-(\d+)/)[1]);
        const childContainer = parentDiv.querySelector(".child-container");

        if (childContainer) {
            const childCheckboxes = childContainer.querySelectorAll("input[type='checkbox']");
            childCheckboxes.forEach(child => {
                child.checked = checkbox.checked;
            });
        }

        updateParentState(checkbox);
    }

    function updateParentState(checkbox) {
        const currentLevel = parseInt(checkbox.className.match(/checkbox-level-(\d+)/)[1]);
        const parentDiv = checkbox.closest(".child-container")?.closest(".lev" + (currentLevel - 1));
        console.log(parentDiv);

        if (parentDiv) {
            const parentCheckbox = parentDiv.querySelector("input[type='checkbox']"); // Récupérer la case à cocher parente
            // Récupérer les cases à cocher frères (on retire la case à cocher parente qui s'inclut aussi)
            const siblingCheckboxes = parentDiv.querySelectorAll(".child-container input[type='checkbox']:not(#" + parentCheckbox.id + ")");


            const allChecked = Array.from(siblingCheckboxes).every(child => child.checked); // Vérifier si tous les enfants sont cochés
            const someChecked = Array.from(siblingCheckboxes).some(child => child.checked); // Vérifier si au moins un enfant est coché

            // Mise à jour de l'état du parent
            if (allChecked) {
                parentCheckbox.checked = true;
                parentCheckbox.indeterminate = false;
            } else if (someChecked) {
                parentCheckbox.checked = false;
                parentCheckbox.indeterminate = true;
            } else {
                parentCheckbox.checked = false;
                parentCheckbox.indeterminate = false;
            }

            // Mise a jours récurcive des parents
            updateParentState(parentCheckbox);
        }
    }


    function toggleVisibility(containerId) {
        const container = document.getElementById(containerId);
        if (container.style.display === "none") {
            container.style.display = "block";
        } else {
            container.style.display = "none";
        }
    }

    /*
    * Fonction qui permet de récupérer les filtres sélectionnés
    * Ne retourne que la catégorie la plus haute
    * @return {Array} Liste des filtres sélectionnés
     */
    function getSelectedFilters() {
        const selectedFilters = [];

        // Récupérer toutes les cases cochées
        let checkboxes = document.querySelectorAll("input[type='checkbox']:checked");
        checkboxes = Array.from(checkboxes);
        // Tri les cases cochées par ordre de profondeur
        checkboxes.sort((a, b) => {
            const aIdSize = a.length;
            const bIdSize = b.length;
            if (aIdSize === bIdSize) {
                return a.value.localeCompare(b.value);
            }
            return aIdSize - bIdSize;
        });
        // Ajouter les filtres sélectionnés à la liste (un seul filtre par catégorie)
        checkboxes.forEach(checkbox => {
            if (checkbox["id"].startsWith('checkbox_')) {
                const ids = checkbox["id"].replace('checkbox_', '').split('-');
                let alreadyAdded = false;
                const id = [];
                ids.forEach((value, index) => {
                    id[index] = parseInt(value);
                    if (selectedFilters.includes(id[index])) {
                        alreadyAdded = true;
                    }
                });
                if (!alreadyAdded) {

                    selectedFilters.push(parseInt(ids[ids.length - 1]));
                }
            }
        });

        return selectedFilters;
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Sélectionner le formulaire
        const form = document.getElementById('formfilter');

        form.addEventListener('submit', function (event) {
            // Empêcher la soumission par défaut
            event.preventDefault();


            document.getElementById('filterValue').value = JSON.stringify(getSelectedFilters());

            // Soumettre le formulaire
            form.submit();
        });
    });

    /*
    * Fonction qui permet de charger les boissons (rechargement dynamique)
     */
    function loadBoisson() {
        // recupere la valeur filtre dans l'url
        const urlParams = new URLSearchParams(window.location.search);
        const filter = urlParams.get('filter');
        const value = urlParams.get('search');
        const filterUrl = filter ? `filter=${filter}` : '';
        const valueUrl = value ? `search=${value}` : '';
        // ajouter le ? si au moins un paramètre est présent
        const separator = filter || value ? '?' : '';
        const url = `squellettes/Boissons.php${separator}${valueUrl}${filter && value ? '&' : ''}${filterUrl}`; // requete sur la page Boissons.php
        if (value !== '') {
            fetch(url) // requete sur la page Boissons.php
                .then(response => response.text())
                .then(data => {
                    document.getElementById('container_Boissons').innerHTML = data;
                });

        }
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

<div class="menuFilter">
    <?php include 'squellettes/filterMenu.php'; ?>
    <form id="formfilter">
        <input type="hidden" name="page" value="ListeBoissons">
        <input type="hidden" id="filterValue" name="filter" value="">
        <button type="submit">Valider</button>
    </form>
</div>
<div id="container_Boissons" class="container_Boissons">

</div>