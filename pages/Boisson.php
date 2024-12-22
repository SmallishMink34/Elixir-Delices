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
                return JSON.parse(data); // Parse explicitement le JSON
            })
            .then(parsedData => {
                console.log('Parsed data:', parsedData);
                parsedData["ingredients"].forEach(item => {
                    document.getElementById('Ingredients').innerHTML += `<p>${item.quantite} ${item.unite} <a href="?page=Ingredient&ing=${item.id}">${item.nom}</a></p>`;
                });
                document.getElementById('titre').innerHTML = parsedData['titre'];
                document.getElementById('titreNav').innerHTML = parsedData['titreFormat'];
                document.getElementById('preparation').innerHTML = parsedData['preparation'];
                document.getElementById('image').src = `${parsedData['image']}`;
                document.getElementById('nomBoisson').innerHTML = parsedData['titre'];
                if (parsedData['previous_name'] != null){
                    document.getElementById('navPrev').href = `?page=Boisson&drink=${parsedData['previous']}`;
                }else{
                    document.getElementById('navPrev').innerHTML = "";
                }
                if (parsedData['next_name'] != null){
                    document.getElementById('navNext').href = `?page=Boisson&drink=${parsedData['next']}`;
                }else{
                    document.getElementById('navNext').innerHTML = "";
                }
                
                
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
<div class="content">
        <img id="image" src="" alt="">
        <div id="boissonInfo">
            <h1 id="titre">aaa</h1>
            <p id="preparation">Rien</p>
            <div id="Ingredients"></div>
        </div>
    </div>
</div>
<div id="nav">
    <a id="navPrev" href=""><</a>
    <p id="titreNav"> Boisson </p>
    <a id="navNext">></a>
</div>