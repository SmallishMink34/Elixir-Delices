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
    <h1 class="titrePage">Créer un compte ! </h1>
    <form id="signup" action="../config/inscription.php" method="POST">
        <span class="titre"><h1>S'inscrire</h1></span>
        <div class="inputs">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" placeholder="Entrez votre Nom" required>
            <span class="error" id="nomError"></span>
        </div>

        <div class="inputs">
            <label for="prenom">Nom :</label>
            <input type="text" id="prenom" name="prenom" placeholder="Entrez votre Prénom" required>
            <span class="error" id="prenomError"></span>
        </div>

        <div class="inputs">
            <label for="email">Email :</label>
            <input type="email" placeholder="Entrez votre email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" id="email" name="email" required>
            <span class="error" id="emailError"></span>
        </div>
        <div class="inputs">
            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" placeholder="Entrez vore Mot de passe" id="mot_de_passe" name="mot_de_passe" required>
            <span class="error" id="passwordError"></span>
        </div>
        <button type="submit">S'inscrire</button>
    </form>
    <a class="bottomlink" href="Login.php">Déjà inscrit ? Connectez-vous !</a>
</body>
</html>

<script>
    document.getElementById('signup').addEventListener('submit', function (event) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('mot_de_passe').value;
        const nom = document.getElementById('nom').value;
        if (password.length < 8) {
            document.getElementById('passwordError').innerText = 'Mot de passe trop court';
            event.preventDefault();

        }
        else if (!/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
            document.getElementById('passwordError').innerText = 'Mot de passe doit contenir au moins une majuscule et un chiffre.';
            event.preventDefault();
        }
        if (nom.trim().length < 2) {
            document.getElementById('nomError').innerText = 'Nom trop court';
            event.preventDefault();
        } else if (!/^[a-zA-Z-]+$/.test(nom)) {
            document.getElementById('nomError').innerText = 'Nom invalide (lettres ou - uniquement)';
            event.preventDefault();
        }

        if (prenom.trim().length < 2) {
            document.getElementById('prenomError').innerText = 'Prénom trop court';
            event.preventDefault();
        } else if (!/^[a-zA-Z]+$/.test(prenom)) {
            document.getElementById('prenomError').innerText = 'Prénom invalide (lettres uniquement)';
            event.preventDefault();
        }
    });
</script>