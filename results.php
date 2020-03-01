<?php
//on récupère les écoles en fonctions des paramètres rentrés
if (!(isset($_POST["diplome_rgp"]) && isset($_POST["sect_disciplinaire_lib"]) && isset($_POST["reg_ins_lib"]))){
    header('Location: index.php'); // on redirect si les paramètres ne sont pas remplis
}

?>

<!doctype html>
<html lang="fr">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <!-- Permet de Track cette page sur Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-159294982-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-159294982-1');
    </script>

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="static/css/styles.css">
    <link rel="stylesheet" href="static/css/results.css">
    <link rel="stylesheet" href="static/css/leaflet.css">
    <link href="https://fonts.googleapis.com/css?family=Fredoka+One&display=swap" rel="stylesheet">

    <title>Résultats</title>
</head>
<body>

<div id="loader">
    <div class="loader"></div>
</div>
<div class="container">
    <div id="normal" style="display: none;"><h1>Voici les résultats !</h1>
        <h2>On a trouvé un total de <span id="nbRecords"></span>  résultats !</h2>
        <div class="results">
            <div class="table">
                <div class="tbl-header">
                    <table style="padding: 0;border-spacing : 0;border: none;">
                        <thead>
                        <tr>
                            <th>Ecole<img alt="Flèche de changement d'ordre" src="static/img/up-arrow.png" class="order" id="0"/></th>
                            <th>Ville<img alt="Flèche de changement d'ordre" src="static/img/up-arrow.png" class="order" id="1"/></th>
                            <th>Specialite<img alt="Flèche de changement d'ordre" src="static/img/up-arrow.png" class="order" id="2"/></th>
                            <th>Type de Diplôme<img alt="Flèche de changement d'ordre" src="static/img/up-arrow.png" class="order" id="3"/></th>
                            <th>Intitulé de la Formation <img alt="Flèche de changement d'ordre" src="static/img/up-arrow.png" class="order" id="4"/></th>
                            <th>Nombre d'apparitions</th>
                            <th>Plus D'Informations</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="tbl-content">
                    <table style="padding: 0;border-spacing : 0;border: none;">
                        <tbody id="body-table"> <!-- est rempli par du JavaScript et non par le PHP (Permet de faire des actions de tri sans avoir a raffraichir la page -->


                        </tbody>
                    </table>
                </div>
                <div class="searchBar"><input type="search" id="searchInput" placeholder="Rechercher ..."><img src="static/img/search.png" alt="loupe" id="searchImage"></div>

            </div>
            <div id="map">
            </div>
        </div>

        <h2>Une autre formation ?</h2>
        <h2><a href="index.php">Retourner a l'acceuil</a></h2>
    </div>
    <div id="noResults" style="display: none;">
        <h1> Aucun Résultat trouvé !</h1>
        <h2><a href="index.php">Retourner a l'acceuil</a></h2>
    </div>


</div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="static/js/leaflet.js"></script>
<script>
    <?php

            printf("var paramQuerry = {
                'diplome_rgp':'%s',
                'sect_disciplinaire_lib':'%s',
                'reg_ins_lib':'%s',
                "
            ,$_POST["diplome_rgp"],$_POST["sect_disciplinaire_lib"],$_POST["reg_ins_lib"]);
            if(isset($_POST["dep_etab_lib"])) printf("'dep_etab_lib':'%s',
            ",$_POST["dep_etab_lib"]);
            if(isset($_POST["etablissement_lib"])) printf("'etablissement_lib':'%s',
            ",$_POST["etablissement_lib"]);
            echo "};"
    ?>

</script>
<script src="static/js/results.js"></script>
</body>


</html>