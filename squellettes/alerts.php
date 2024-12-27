<?php
?>
<div id="containerAlert">
    <form id="alerts" method="post" action="<?= $action ?>" >
        <h1><?= $title ?? "Titre Inconnu"  ?></h1>
        <p><?= $message ?? "Message Inconnu" ?></p>
        <button type="submit" name="addPanier" value="true"><?= $oui ?? "Oui" ?></button>
        <button type="submit" name="addPanier" value="false"><?= $non ?? "Non" ?></button>
    </form>
</div>