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
                    
                });
            })
            .catch(error => {
                console.error('Erreur dans la requête fetch :', error);
            });
    }    
    getBoissonsInformations(<?php echo $_GET['drink']; ?>);
</script>