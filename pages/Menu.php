
<script>

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
</script>
<nav class="header navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="#">Elixir & Délice</a>
        <div class="centerElement">
            <div class="no-animation Menu" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="#home"><b>Home</b></a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><b>Boissons</b></a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><b>Ingrédients</b></a></li>
                </ul>
            </div>
            <div id="search" class="ParentSeachBox Ghost no-animation"s>
                <form action="search.php" method="get">
                    <div class="searchBox">
                        <button class="searchButton" type="submit" name="submit-search" tabindex="-1"> <?php include("images/icons/search.svg") ?></button>
                        <li class="nav-item"><input name="search" class="nav-link search-bar" type="search" placeholder="Recherche..." tabindex="-1"></li>
                    </div>
                </form>
            </div>
        </div>
        <ul class="d-flex flex-right">
            <li class="nav-item"></li><button class="nav-link" onclick="switchGhostList(['search', 'navbarNav'])"><?php include("images/icons/search.svg") ?></button></li>
            <li class="nav-item"><a class="nav-link" href="#account"><b>Compte</b></Account></a></li>
        </ul>
    </div>
</nav>
