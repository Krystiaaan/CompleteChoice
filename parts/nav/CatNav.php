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
                <form class="search-form" action="#" method="get">
                    <input class="search-input" type="text" name="search" placeholder="Suchen">
                    <button class="search-button" type="submit">Suchen</button>
                </form>
            </li>
        </ul>
    </nav>
</div>
NAVHTML;
