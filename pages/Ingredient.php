<script>

    function traverseData(data, parsedData = [], path = []) {
        data.forEach(item => {
            // Ajout de l'élément courant dans le chemin
            const currentPath = [...path, { ingredient_id: item.ingredient_id, nom: item.ingredient_nom }];

            // Si l'élément n'a pas d'enfants, on l'ajoute comme un chemin complet
            if (!item.children || item.children.length === 0) {
                parsedData.push(currentPath);
            }

            // Si l'élément a des enfants, on les parcourt récursivement
            if (item.children && item.children.length > 0) {
                traverseData(item.children, parsedData, currentPath);
            }
        });
    }

    function getBoissonsInformations (value){
        fetch(`/donnee/IngredientInfo.php?ing=${encodeURIComponent(value)}`)
            .then(response => {
                console.log('Response:', response);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text(); // Change temporairement en texte brut
            })
            .then(data => {
                return JSON.parse(data); // Parse explicitement le JSON
            })
            .then(parsedData => {
                console.log('Parsed data:', parsedData);

                let data = [];
                traverseData(parsedData['hierarchy'], data);

                // Affichage des chemins complets sur plusieurs lignes
                let listContainer = document.createElement('div');
                listContainer.id = 'listSuperContainer';
                data.forEach(path => {
                    let pathHTML = '';
                    path.forEach(item => {
                        pathHTML += `<a href="?page=Ingredient&ing=${item.ingredient_id}">${item.nom}</a> > `;
                    });

                    // Crée un span pour chaque chemin et ajoute un retour à la ligne après chaque chemin
                    const pathSpan = `<span>${pathHTML.slice(0, -3)}</span><br>`;

                    // Ajoute chaque span dans la div englobante
                    listContainer.innerHTML += pathSpan;
                });
                document.getElementById('listSuper').appendChild(listContainer);
                document.getElementById('tableSous').innerHTML = "<tr><th>Sous-Catégorie</th></tr>";
                parsedData['sousCat'].forEach(ingredient => {
                    document.getElementById('tableSous').innerHTML += `<tr><td><a href='?page=Ingredient&ing=${ingredient.id}'> ${ingredient.nom}</td></tr>`;
                });

                document.getElementById('tableRecette').innerHTML = "<tr><th>Recette</th></tr>";
                parsedData['recettes'].forEach(recette => {
                    document.getElementById('tableRecette').innerHTML += `<tr><td><a href='?page=Boisson&drink=${recette.recette_id}'> ${recette.titre}</td></tr>`;
                });

                document.getElementById('tableSuper').innerHTML = "<tr><th>Super-Catégorie</th></tr>";
                parsedData['superCat'].forEach(ingredient => {
                    document.getElementById('tableSuper').innerHTML += `<tr><td><a href='?page=Ingredient&ing=${ingredient.id}'> ${ingredient.nom}</td></tr>`;
                });

                document.getElementById('titre').innerHTML = parsedData['ingredient']['nom'];
                document.getElementById('titreNav').innerHTML = parsedData['ingredient']['nom'];

                document.getElementById('boissonContener').style.display = "flex";

            })
            .catch(error => {
                console.error('Erreur dans la requête fetch :', error);
            });
    }
    getBoissonsInformations(<?php echo $_GET['ing']; ?>);
</script>
<div id="history">
    <a href="?page=Home">Accueil</a> > <a href="?page=ListeBoissons">Liste des Ingrédients</a> >
    <span id="listSuper"></span>
</div>
<div id="boissonContener" class="content">
    <div id="boisson">
        <img id="image" src="../images/icons/ingredients.png" alt="">
    </div>
    <div id="boissonInfo">
        <h1 id="titre">aaa</h1>
        <div id="Ingredients">
            <table id="tableSous" class="tableInfo">

            </table>
            <table id="tableRecette" class="tableInfo">

            </table>
            <table id="tableSuper" class="tableInfo">

            </table>
        </div>
    </div>
</div>
<div id="nav">
    <a id="navPrev" href=""><</a>
    <p id="titreNav"> Ingredient </p>
    <a id="navNext">></a>
</div>