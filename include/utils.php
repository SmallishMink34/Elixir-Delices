<?php
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

function getImage($Path,$titre){
    $imagePath = $Path;
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
    return $imageSrc;
}