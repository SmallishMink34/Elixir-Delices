<?php
    require __DIR__.'/../include/init.php';
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit();
?>
