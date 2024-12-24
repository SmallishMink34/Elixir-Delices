<?php
session_start();
?>

<script>
    var isSearchActive = false;
    changeState(false);

    function switchSearch() {
        isSearchActive = !isSearchActive;
        switchGhostList(['search', 'navbarNav']);
        if (isSearchActive) {
            setTimeout(() => {
                document.getElementById('searchInput').focus();
            }, 500);
        }
    }

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

    function FocusSearch(){
        const search = document.getElementById('search');
        const searchBody = document.getElementById('search_Body');
        search.classList.add('search_active');
        searchBody.classList.add('searchBody_active');
        search.classList.remove('Ghost');

        Autocompletion(document.getElementById('searchInput').value);
    }

    function FocusSearchOut(){
        const search = document.getElementById('search');
        const searchBody = document.getElementById('search_Body');

        search.classList.add('search_unactive');
        search.classList.remove('search_active');
        searchBody.classList.remove('searchBody_active');

        searchBody.innerHTML = "";

        setTimeout(() => {
            search.classList.remove('search_unactive');
        }, 500);
    }

    function getUrl(){
        const url = new URL(window.location.href);
        return url;
    }

    function Autocompletion(value){
        const query = value;
        const searchBody = document.getElementById('listSearch');
        if (value.length == 0) {
            document.getElementById("searchInput").innerHTML = "";
            return;
        } else {
            fetch(`/pages/search.php?q=${encodeURIComponent(query)}`)
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
                    link.href = getUrl().origin + "/?page=Boissons&search=" + item.titre;
                    div.appendChild(link);
                    searchBody.appendChild(div);
                });
            })
            .catch(error => {
                console.error('Erreur dans la requête fetch :', error);
            });
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
    
</script>
<nav class="header navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="?page=home">Elixir & Délice</a>
        <div class="centerElement">
            <div class="no-animation Menu" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="?page=home"><b>Home</b></a></li>
                    <li class="nav-item"><a class="nav-link" href="?page=ListeBoissons"><b>Boissons</b></a></li>
                    <li class="nav-item"><a class="nav-link" href="?page=Ingredients"><b>Ingrédients</b></a></li>
                </ul>
            </div>
            <div id="search" class="ParentSeachBox Ghost no-animation">
                <form action="?page=ListeBoissons" method="get">
                    <div class="searchBox">
                        <input type="hidden" name="page" value="ListeBoissons">
                        <button class="searchButton" type="submit" tabindex="-1" > <?php include("images/icons/search.svg") ?></button> <!-- TODO: modifier le tabindex -->
                        <li class="nav-item"><input id="searchInput" name="search" class="nav-link search-bar" type="search"  oninput="Autocompletion(this.value)" onfocus="FocusSearch()" onblur="FocusSearchOut()"  placeholder="Recherche..." tabindex="-1"></li>
                    </div>
                </form>
            </div>
        </div>
        <ul class="d-flex flex-right">
            <li class="nav-item"></li><button class="nav-link" onclick="switchSearch()"><?php include("images/icons/search.svg") ?></button></li>
            <li class="nav-item flex-center">
                <input type="checkbox" role="button" aria-label="Display the menu" id="toggle" onclick="changeState()" class="menuDropdown">
                <div id="dropdown" class="dropdown invisible" >
                    <ul id="menuItemToDisable">
                        <li class="nav-item"><a href="?page=home">Home</a></li>
                        <li class="nav-item"><a href="?page=ListeBoissons">Boissons</a></li>
                        <li class="nav-item"><a href="?page=Ingredients">Ingrédients</a></li>
                    </ul>
                    <ul>
                        <li><a href="config\intall.php">INSTALL</a></li>
                        <li><a href="?page=Account">Account</a></li>
                        <li><a href="?page=Panier">Panier</a></li>
                        <li><a href="?page=Login">Login</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>
<div id="search_Body">
    <div id="listSearch">

    </div>
</div>

