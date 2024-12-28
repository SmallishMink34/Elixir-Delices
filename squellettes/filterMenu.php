<?php

include_once __DIR__ . '/../include/db.php';

$structure = file_get_contents('http://elixirdelice.byethost16.com/donnee/getStructureFilter.php');


/**
 * Génère le HTML pour un ingrédient et ses sous-éléments
 *
 * @param array $data Données de l'ingrédient
 * @param int $level Niveau de profondeur
 * @return string HTML généré
 */
function generateIngredientToggleHTML($data, $level = 1, $parentId = '') {
    $html = '';
    foreach ($data as $ingredient) {
        // Créer un identifiant unique pour chaque ingrédient
        $uniqueId = $parentId . '-' . $ingredient['ingredient_id'];  // Combine l'ID parent et l'ID de l'ingrédient

        // Créer l'ID de la case à cocher et du conteneur
        $containerId = 'container_' . $uniqueId;
        $checkboxId = 'checkbox_' . $uniqueId;

        $html .= '<div class="lev' . $level . '">';
        $html .= '<input type="checkbox" id="' . $checkboxId . '" class="checkbox-level-' . $level . '" onclick="toggleChildren(this)" />';
        $html .= '<label for="' . $checkboxId . '" class="bold">' . htmlspecialchars($ingredient['ingredient_nom']) . '</label>';

        // Ajouter un bouton pour afficher/masquer les enfants si des sous-éléments existent
        if (!empty($ingredient['sous_elements'])) {
            $html .= ' <button type="button" onclick="toggleVisibility(\'' . $containerId . '\')">Afficher/Masquer</button>';
            $html .= '<div id="' . $containerId . '" class="child-container" style="display: none;">';
            $html .= generateIngredientToggleHTML($ingredient['sous_elements'], $level + 1, $uniqueId);  // Passer l'ID unique au niveau suivant
            $html .= '</div>';
        }

        $html .= '</div>';
    }

    return $html;
}

echo generateIngredientToggleHTML(json_decode($structure, true));

?>
