<?php
session_start();
?>

<script>
    let isSearchActive = false;
    changeState(false);

    /**
     * Permet de switcher entre la barre de recherche et le menu
     */
    function switchSearch() {
        isSearchActive = !isSearchActive;
        switchGhostList(['search', 'navbarNav']);
        if (isSearchActive) {
            setTimeout(() => {
                document.getElementById('searchInput').focus();
            }, 500);
        }
    }

    /*
        * Permet de switcher entre les éléments de la liste (invisible ou visible)
     */
    function switchGhostList(ids) {
        ids.forEach(id => switchGhost(id));
    }

    function switchGhost(id) {
        const ghost = document.getElementById(id);
        if (ghost.classList.contains('no-animation')) {
            ghost.classList.remove('no-animation');
        }
        if (ghost.classList.contains('Ghost')) {
            ghost.classList.remove('Ghost');
        } else {
            ghost.classList.add('Ghost');
            
        }
    }

    /**
     * Permet de focus la barre de recherche (joue une animation)
     */
    function FocusSearch(){
        const search = document.getElementById('search');
        const searchBody = document.getElementById('search_Body');
        const blackground = document.getElementById('blackground');
        blackground.classList.remove('invisible');
        search.classList.add('search_active');
        searchBody.classList.add('searchBody_active');
        searchBody.classList.remove('invisible');

        search.classList.remove('Ghost');

        Autocompletion(document.getElementById('searchInput').value);
    }

    /**
     * Permet de focus out la barre de recherche (joue une animation)
     */
    function FocusSearchOut(){
        const search = document.getElementById('search');
        const searchBody = document.getElementById('search_Body');
        const blackground = document.getElementById('blackground');
        const listSearch = document.getElementById('listSearch');

        blackground.classList.add('invisible');
        search.classList.add('search_unactive');
        search.classList.remove('search_active');
        searchBody.classList.remove('searchBody_active');
        searchBody.classList.add('invisible');

        listSearch.innerHTML = "";

        setTimeout(() => {
            search.classList.remove('search_unactive');
        }, 500);
        console.log('Focus out');
    }


    /*
        * Permet de récupérer l'url actuelle
     */
    function getUrl(){
        const url = new URL(window.location.href);
        return url;
    }

    /**
     * Permet de faire une requête de recherche pour l'autocomplétion
     * @param {string} value
     */
    function Autocompletion(value){
        const query = value;
        const searchBody = document.getElementById('listSearch');
        const searchType = document.querySelector('input[name="typesearch"]:checked').value;
        if (value.length == 0) {
            document.getElementById("listSearch").innerHTML = "";
            return;
        } else {
            const valuetoAdd = `&t=${encodeURIComponent(searchType)}`;

            fetch(`/donnee/search.php?q=${encodeURIComponent(query)}${valuetoAdd}`)
            .then(response => {
                console.log('Response:', response);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text(); // Change temporairement en texte brut
            })
            .then(data => {
                return JSON.parse(data); // Parse explicitement le JSON
            })
            .then(parsedData => {
                searchBody.innerHTML = "";
                console.log('Parsed data:', parsedData);
                parsedData.forEach(item => {
                    const div = document.createElement('div');
                    const link = document.createElement('a');
                    link.innerHTML = item.titre;
                    if (searchType === 'ing') {
                        link.href = getUrl().origin + "index.php?page=Ingredients&search=" + item.titre;
                    } else if (searchType === 'bois') {
                        link.href = getUrl().origin + "index.php?page=Boissons&search=" + item.titre;
                    }
                    div.appendChild(link);
                    searchBody.appendChild(div);
                });
            })
            .catch(error => {
                console.error('Erreur dans la requête fetch :', error);
            });
        }
    }

    function changeFormRedired(value){
        const search = document.getElementById('page');
        const searchInput = document.getElementById('searchInput');
        Autocompletion(searchInput.value);
        if (value === 'ing') {
            search.value = 'ListeIngredients';
        } else {
            search.value = 'ListeBoissons';
        }
    }

    function changeState(value = false){
        const toggle = document.getElementById('toggle');
        if (value) {
            toggle.checked = value;
        }
        if (toggle.checked) {
            document.getElementById('dropdown').classList.remove('invisible');
        } else {
            document.getElementById('dropdown').classList.add('invisible');
        }
    }
    window.addEventListener('click', function(e){
        if (e.target.id !== 'toggle' && e.target.id !== 'dropdown' && e.target.id !== 'menuItemToDisable') {
            changeState(false);
        }
    });

</script>
<nav class="header navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="?page=home">Elixir & Délice</a>
        <div class="centerElement">
            <div class="no-animation Menu" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="?page=home"><b>Home</b></a></li>
                    <li class="nav-item"><a class="nav-link" href="?page=ListeBoissons"><b>Boissons</b></a></li>
                    <li class="nav-item"><a class="nav-link" href="?page=ListeIngredients"><b>Ingrédients</b></a></li>
                </ul>
            </div>
            <div id="search" class="ParentSeachBox Ghost no-animation">
                <form id="searchForm" method="get">
                    <div class="searchBox">
                        <input type="hidden" id="page" name="page" value="ListeBoissons">
                        <button class="searchButton" type="submit" tabindex="-1" > <?php include("images/icons/search.svg") ?></button> <!-- TODO: modifier le tabindex -->
                        <li class="nav-item"><input id="searchInput" name="search" class="nav-link search-bar" type="search"  oninput="Autocompletion(this.value)" onfocus="FocusSearch()" placeholder="Recherche..." tabindex="-1"></li>
                    </div>
                </form>
            </div>
        </div>
        <ul class="d-flex flex-right menuBtnDroite">
            <li class="nav-item"></li><button class="nav-link" onclick="switchSearch()"><?php include("images/icons/search.svg") ?></button></li>
            <li class="nav-item flex-center">
                <input type="checkbox" role="button" aria-label="Display the menu" id="toggle" onclick="changeState()" class="menuDropdown">
                <div id="dropdown" class="dropdown invisible" >
                    <ul id="menuItemToDisable">
                        <li class="nav-item"><a href="?page=home">Home</a></li>
                        <li class="nav-item"><a href="?page=ListeBoissons">Boissons</a></li>
                        <li class="nav-item"><a href="?page=ListeIngredients">Ingrédients</a></li>
                    </ul>
                    <ul>
                        <li><a href="config\intall.php">INSTALL</a></li>
                        <li><a href="?page=Account">Account</a></li>
                        <li><a href="?page=Panier">Panier</a></li>
                        <?php if (isset($_SESSION['user'])) {
                            echo '<li><a href="pages/Logout.php">Logout</a></li>';
                        } else {
                            echo '<li><a href="pages/Login.php">Login</a></li>';
                        }?>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>
<div id="search_Body" class="search_body invisible">
    <button id="blackground" class="blackground invisible" onclick="FocusSearchOut()"></button>
    <div class="choice">

        <input id="bois" name="typesearch" type="radio" onclick="changeFormRedired('bois')" value="bois" checked>
        <label for="bois">Boissons</label>

        <input id="ing" name="typesearch" type="radio" value="ing" onclick="changeFormRedired('ing')" >
        <label for="ing">Ingrédients</label>

    </div>
    <div id="listSearch">

    </div>
</div>

