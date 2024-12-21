<script>
    function getBoissonsInformations (value){
        fetch(`/config/BoissonInfo.php?drink=${encodeURIComponent(value)}`)
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
                parsedData.forEach(item => {
                    document.getElementById('Ingredients').innerHTML += `<p>${item.quantite} <a href="?page=Ingredient&ing=${item.id}">${item.nom}</a></p>`;
                });
                document.getElementById('titre').innerHTML = parsedData[0].titre;
                document.getElementById('preparation').innerHTML = parsedData[0].preparation;
                document.getElementById('image').src = `${parsedData[0].image}`;
                document.getElementById('nomBoisson').innerHTML = parsedData[0].titre;
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
        <h1 id="titre">aaa</h1>
        <p id="preparation">Rien</p>
        <div id="Ingredients">

        </div>
    </div>

</div>