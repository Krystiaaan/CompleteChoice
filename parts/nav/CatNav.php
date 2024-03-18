<?php
echo <<< NAVHTML

<div class="container">
    <nav class="nav-box second-nav">
        <ul class="CatNavUl">
            <li class="catNavLi"><a href="Index.php">Alle</a></li>
            <li class="catNavLi"><a href="ShowByCategory.php?cat=Smartphones">Smartphones</a></li>
            <li class="catNavLi"><a href="ShowByCategory.php?cat=Hardware">Hardware</a></li>
            <li class="catNavLi"><a href="ShowByCategory.php?cat=Elektronik">Elektronik</a></li>   
            <li class="catNavLi"><a href="ShowByCategory.php?cat=Bücher">Bücher</a></li>
            <li class="catNavLiForm">

                    <input class="search-input" id="search-input" type="text" name="search" placeholder="Suchen">
                    <button class="search-button" type="submit" onclick="search.requestData()">Suchen</button>
                <div id="livesearch"></div>
            </li>
        </ul>
    </nav>
</div>
<script src="js/suche.js"></script>
NAVHTML;
