<?php
    $page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'home';
    $pageFile = 'pages/' . $page . '.php';
    $styleFile = 'style/' . $page . '.css';
    if (!file_exists($pageFile)) {
        $pageFile = 'pages/404.php';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/style/base.css">
    <link rel="stylesheet" href="/style/index.css">
    <link rel="stylesheet" href="/<?= $styleFile ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elixir & Délice</title>
</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<body>
    
    <header>
        <?php include 'pages/Menu.php';?>
    </header>
    <main>
        <div class="container">
            <?php include $pageFile; // Inclusion du contenu dynamique ?>
        </div>
    </main>
</body>
</html>