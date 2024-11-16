<?php

/*
    (?:^|\|) : début de ligne ou un |
    \s* : espaces
    (\d+)? : chiffre (optionnel)
    \s* : espace
    (cl |g |kg |ml |l |lb )? : unité (optionnel)
    (.+?) : nom de l'ingrédient
    (?=\||$) : suivi d'un | ou de la fin de la ligne
*/
$regex = '/(?:^|\|)\s*(\d+)?\s*(cl |g |kg |ml |l |lb )?\s*(.+?)(?=\||$)/';




$sentence = '1 pomme|1 citron|6 glaçons|10 cl de jus de pomme|5 cl de calvados|10 cl de crème de cassis|champagne|un peu de cassis|morceaux de pommes|morceaux d\'ananas';

// Appliquer preg_match_all
preg_match_all($regex, $sentence, $matches);

// Afficher les résultats
foreach ($matches[0] as $key => $fullMatch) {
    echo "Ingrédient complet : " . $fullMatch . PHP_EOL;
    echo "Quantité : " . ($matches[1][$key] ?? "aucune") . PHP_EOL;
    echo "Unité : " . ($matches[2][$key] ?? "aucune") . PHP_EOL;
    echo "Élément : " . $matches[3][$key] . PHP_EOL;
    echo "-------------------" . PHP_EOL;
}
?>
