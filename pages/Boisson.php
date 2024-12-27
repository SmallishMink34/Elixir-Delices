<script>
    function getBoissonsInformations (value){
        fetch(`/donnee/BoissonInfo.php?drink=${encodeURIComponent(value)}`)
            .then(response => {
                console.log('Response:', response);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text(); // Change temporairement en texte brut
            })
            .then(data => {
                try {
                    const parsedData = JSON.parse(data);
                    if (!parsedData || typeof parsedData !== "object") {
                        throw new Error("Données invalides reçues");
                    }
                    return parsedData;
                } catch (e) {
                    console.error("Erreur lors du parsing JSON :", e, data);
                    throw e;
                }
            })
            .then(parsedData => {
                const drinkData = Object.values(parsedData)[0];
                console.log('Parsed data:', parsedData);
                if (Array.isArray(drinkData["ingredients"])) {
                    const ingredients = document.getElementById('tableIngredients');
                    ingredients.innerHTML = "<tr><th>Quantité</th><th>Unité</th><th>Ingrédient</th></tr>";
                    for (const ingredient of drinkData["ingredients"]) {
                        ingredients.innerHTML += `<tr><td>${ingredient.quantite || ''}</td><td>${ingredient.unite || ''}</td><td><a href='?page=Ingredient&ing=${ingredient.id}'> ${ingredient.nom}</td></tr>`;

                    }

                } else {
                    console.warn('Ingredients is not an array or is missing:', drinkData["ingredients"]);
                }
                document.getElementById('titre').innerHTML = drinkData['titre'];
                document.getElementById('titreNav').innerHTML = drinkData['titreFormat'];
                document.getElementById('preparation').innerHTML = drinkData['preparation'];
                document.getElementById('image').src = `${drinkData['image']}`;
                document.getElementById('nomBoisson').innerHTML = drinkData['titre'] + " #" + drinkData['id'];
                if (drinkData['previous_name'] != null){
                    document.getElementById('navPrev').href = `?page=Boisson&drink=${drinkData['previous']}`;
                }else{
                    document.getElementById('navPrev').innerHTML = "";
                }
                if (drinkData['next_name'] != null){
                    document.getElementById('navNext').href = `?page=Boisson&drink=${drinkData['next']}`;
                }else{
                    document.getElementById('navNext').innerHTML = "";
                }

                document.getElementById('boissonContener').style.display = "flex";
            })
            .catch(error => {
                console.error('Erreur dans la requête fetch :', error);
            });
    }

    getBoissonsInformations(<?php echo $_GET['drink']; ?>);
</script>
<div id="history">
    <a href="?page=Home">Accueil</a> > <a href="?page=ListeBoissons">Liste des boissons</a> > <span id="nomBoisson"></span>

</div>
<div id="boissonContener" class="content">
    <div id="boisson">
        <img id="image" src="" alt="">
    </div>
        <div id="boissonInfo">
            <h1 id="titre">aaa</h1>
            <p id="preparation">Rien</p>
            <div id="Ingredients">
                <table id="tableIngredients">

                </table>
            </div>
        </div>
    </div>
</div>
<div id="nav">
    <a id="navPrev" href=""><</a>
    <p id="titreNav"> Boisson </p>
    <a id="navNext">></a>
</div>