
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

    function FocusSearch(){
        const search = document.getElementById('search');
        search.classList.add('search_active');
        search.classList.remove('Ghost');
    }

    function FocusSearchOut(){
        const search = document.getElementById('search');
        search.classList.add('search_unactive');
        search.classList.remove('search_active');
        setTimeout(() => {
            search.classList.remove('search_unactive');
        }, 500);
    }

    function Autocompletion(){
        const search = document.getElementById('searchInput');
        console.log(search.value);
    }
    
</script>
<nav class="header navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="?page=home">Elixir & Délice</a>
        <div class="centerElement">
            <div class="no-animation Menu" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="?page=home"><b>Home</b></a></li>
                    <li class="nav-item"><a class="nav-link" href="?page=Boissons"><b>Boissons</b></a></li>
                    <li class="nav-item"><a class="nav-link" href="?page=Ingrédients"><b>Ingrédients</b></a></li>
                </ul>
            </div>
            <div id="search" class="ParentSeachBox Ghost no-animation">
                <form action="?page=Boissons" method="get">
                    <div class="searchBox">
                        <input type="hidden" name="page" value="Boissons">
                        <button class="searchButton" type="submit" tabindex="-1" > <?php include("images/icons/search.svg") ?></button> <!-- TODO: modifier le tabindex -->
                        <li class="nav-item"><input id="searchInput" name="search" class="nav-link search-bar" type="search"  oninput="Autocompletion()" onfocus="FocusSearch()" onblur="FocusSearchOut()"  placeholder="Recherche..." tabindex="-1"></li>
                    </div>
                </form>
            </div>
        </div>
        <ul class="d-flex flex-right">
            <li class="nav-item"></li><button class="nav-link" onclick="switchGhostList(['search', 'navbarNav'])"><?php include("images/icons/search.svg") ?></button></li>
            <li class="nav-item"><a class="nav-link" href="?page=account"><b>Compte</b></Account></a></li>
        </ul>
    </div>
</nav>
