<?php
require __DIR__.'/../include/init.php';

function formatString($string): string
{
    // Convertit des caractères accentué en caracteres non accentués
    $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    
    // supprime les caractères spéciaux (sauf les espaces)
    $string = preg_replace('/[^a-zA-Z\s]/', '', $string);
    // Remplace les espaces par des _
        $string = str_replace(' ', '_', $string);
    
    // Met la premiere lettre en majuscule6+
    $string = ucwords(strtolower($string));
    

    
    $words = explode('_', $string);
    $words = array_slice($words, 0, 3);
    return implode('_', $words);
}

function formatTitre($titre): string
{
    preg_match('/^([^(,:]+(?:\s[^(,:]+){0,2})/', $titre, $matches);
    return $matches[0];
}

function getImage($Path,$titre, $type="boisson"): string
{
    $imagePath = $Path;
    $image = formatString($titre);
    // echo "$imagePath$image";
    if (file_exists($imagePath . $image . ".jpg")) {
        $imageSrc = $imagePath . $image . ".jpg";
    }
    // Sinon vérifier si l'image avec extension .png existe
    elseif (file_exists($imagePath . $image . ".png")) {
        $imageSrc = $imagePath . $image . ".png";
    }
    // Si aucune image n'existe, utiliser une image par défaut
    else {
        if ($type === 'boisson') {
            $imageSrc = "/images/icons/default_image.jpg"; // Image par défaut
        } else {
            $imageSrc = "/images/icons/ingredients.png"; // Image par défaut
        }
//        $imageSrc = $imagePath . $image . ".png";
    }
    return $imageSrc;
}

function checkInBasket($id): bool
{
    if (isset($_SESSION['user'])) {
        foreach ($_SESSION['panier'] as $key => $value) {
            if ($value == $id) {
                return true;
            }
        }
    }else if (isset($_COOKIE['panier'])) {
        $basket = json_decode($_COOKIE['panier'], true);
        foreach ($basket as $key => $value) {
            if ($value == $id) {
                return true;
            }
        }
    }
    return false;
}

function checkInFav($id): bool
{
    if (isset($_SESSION['user'])) {
        foreach ($_SESSION['favoris'] as $key => $value) {
            if ($value['id'] == $id) {
                return true;
            }
        }
    }
    return false;
}

function viderCookie($nomCookie)
{
    setcookie($nomCookie, '', time() - 3600, '/');
}

function getCookieData($cookieName)
{
    return isset($_COOKIE[$cookieName]) ? json_decode($_COOKIE[$cookieName], false) : null;
}
