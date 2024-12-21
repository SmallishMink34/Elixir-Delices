<?php
require 'include/db.php';
require 'include/utils.php';
$search = (isset($_GET['search'])) ? strtolower($_GET['search']) : '';

$pdo = getDatabaseConnection();
$stmt = $pdo->query("SELECT * FROM Recette where lower(titre) like '%$search%'");
$cases = $stmt->fetchAll();

$listOfValidFilters = ['search'];


?>
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
<div class="container_Boissons">
    <?php foreach ($cases as $case) {
        $id = $case['id'];
        $titre = $case['titre'];
        $imageSrc = getImage("/images/Photos/", $titre);
        
        include 'squellettes/BoissonEtiquettes.php';
    } ?>
</div>