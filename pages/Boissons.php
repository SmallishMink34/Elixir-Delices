<?php
require 'include/db.php';

$search = (isset($_GET['search'])) ? strtolower($_GET['search']) : '';

$pdo = getDatabaseConnection();
$stmt = $pdo->query("SELECT * FROM Recette where lower(titre) like '%$search%'");
$cases = $stmt->fetchAll();

$listOfValidFilters = ['search'];

function formatString($string) {
    // Convertit des caractères accentuée en caractères non accentués
    $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    
    // supprime les caractères spéciaux (sauf les espaces)
    $string = preg_replace('/[^a-zA-Z\s]/', '', $string);
    // Remplace les espaces par des _
        $string = str_replace(' ', '_', $string);
    
    // Met la premiere lettre en majuscule6+
    $string = ucwords(strtolower($string));
    

    
    $words = explode('_', $string);
    $words = array_slice($words, 0, 3);
    $string = implode('_', $words);
    
    
    return $string;
}

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
        $imagePath = "/images/Photos/";
        $image = formatString($titre);
        // echo "$imagePath$image";
        if (file_exists("./".$imagePath . $image . ".jpg")) {
            $imageSrc = $imagePath . $image . ".jpg";
        }
        // Sinon vérifier si l'image avec extension .png existe
        elseif (file_exists("./".$imagePath . $image . ".png")) {
            $imageSrc = $imagePath . $image . ".png";
        }
        // Si aucune image n'existe, utiliser une image par défaut
        else {
            $imageSrc = "/images/Photos/default_image.jpg"; // Image par défaut
        }


        include 'squellettes/BoissonEtiquettes.php';
    } ?>
</div>