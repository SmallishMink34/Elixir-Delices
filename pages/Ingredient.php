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
<div class="content">
        <h1 id="titre">aaa</h1>
        <p id="preparation">Rien</p>
        <div id="Ingredients">

        </div>
    </div>

</div>