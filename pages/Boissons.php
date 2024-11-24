<?php
require 'include/db.php';

$search = (isset($_GET['search'])) ? strtolower($_GET['search']) : '';

$pdo = getDatabaseConnection();
$stmt = $pdo->query("SELECT * FROM Recette where lower(titre) like '%$search%'");
$cases = $stmt->fetchAll();

$listOfValidFilters = ['search'];

?>
<div class="filterCase">
    <?php foreach ($_GET as $key => $value) {
        if (in_array($key, $listOfValidFilters)) {
            include 'squellettes/filter.php';
        }
        
    } ?>
</div>
<div class="container_Boissons">
    <?php foreach ($cases as $case) {
        $titre = $case['titre'];
        $image = "Mojito.jpg";
        include 'squellettes/BoissonEtiquettes.php';
    } ?>
</div>