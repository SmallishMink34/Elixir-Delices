<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/style/base.css">
    <link rel="stylesheet" href="/style/index.css">
    <link rel="stylesheet" href="/style/Login.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elixir & Délice</title>
</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<body>
    <h1 class="titrePage">Connecte toi ! </h1>
    <form id="signup" action="../config/connexion.php" method="POST">
        <span class="titre"><h1>Connexion</h1></span>
        <?php  if (isset($_GET['success']) && $_GET['success'] == 1 && !isset($_GET['error'])) {
            echo '<span class="success">Inscription réussie !</span>';
        }?>
        <div class="inputs">
        <label for="email">Email :</label>
        <input type="email" placeholder="Entrez votre email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" id="email" name="email" required><br>
        </div>
        <div class="inputs">
        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" placeholder="Entrez vore Mot de passe" id="mot_de_passe" name="mot_de_passe" required><br>
        </div>

        <?php
        if (isset($_GET['error'])) {
            echo '<span class="error">Email ou mot de passe incorrect</span>';
        }
        ?>
        <button type="submit">Se connecter</button>
    </form>
    <a class="bottomlink" href="Signup.php">Pas encore inscrit ? Inscrivez-vous !</a>
</body>
</html>