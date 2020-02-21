<?php

$json = file_get_contents("https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&sort=-rentree_lib&facet=diplome_rgp&facet=sect_disciplinaire_lib&facet=reg_ins_lib&refine.rentree_lib=2017-18");
$infos = json_decode($json,true);


?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="static/css/styles.css">
    <link rel="stylesheet" href="static/css/index.css">

    <link href="https://fonts.googleapis.com/css?family=Fredoka+One&display=swap" rel="stylesheet">
    <title>Trouve ma Formation</title>
</head>
<body>
<div class="container">
    <div id="alerts">
        <h1>Une erreur a été detectée !</h1>
        <br>
        <p>Aucun résultat ne sera trouvé avec cette recherche</p>
    </div>
    <h1>Trouve ma Formation!</h1>
        <form action="results.php" method="post">
            <h2>Paramètres Obligatoires</h2>
            <div class="buttons" id="required">
                <div class="select">
                    <select name="diplome_rgp" required> <!-- src : https://codepen.io/raubaca/pen/VejpQP -->
                        <option value="" selected disabled>Type de Diplôme<span style="color: #920000 !important;">*</span></option>
                        <?php
                        foreach ($infos["facet_groups"][2]["facets"] as $spec){
                            printf("<option value=\"%s\">%s</option>",$spec["name"],$spec["name"]);
                        }
                        ?>
                    </select>
                </div>
                <div class="select" >
                    <select name="sect_disciplinaire_lib" required> <!-- src : https://codepen.io/raubaca/pen/VejpQP -->
                        <option value="" selected disabled>Spécialité<span style="color: #920000 !important;">*</span></option>
                        <?php
                        foreach ($infos["facet_groups"][1]["facets"] as $spec){
                            printf("<option value=\"%s\">%s</option>",$spec["name"],$spec["name"]);
                        }
                        ?>
                    </select>
                </div>
                <div class="select">
                    <select name="reg_ins_lib" required> <!-- src : https://codepen.io/raubaca/pen/VejpQP -->
                        <option value="" selected disabled>Région<span style="color: #920000 !important;">*</span></option>
                        <?php
                        foreach ($infos["facet_groups"][0]["facets"] as $reg){
                            printf("<option value=\"%s\">%s</option>",$reg["name"],$reg["name"]);
                        }
                        ?>
                    </select>
                </div>
            </div>
            <h2>Paramètres Optionnels</h2>
            <div class="buttons" id="optional">
                <div class="select" style="display: inline-flex;">
                    <select name="dep_etab_lib" > <!-- src : https://codepen.io/raubaca/pen/VejpQP -->
                        <option value="" selected disabled>Département</option>
                    </select>
                </div>
                <div class="select" style="display: inline-flex;">
                    <select name="etablissement_lib" > <!-- src : https://codepen.io/raubaca/pen/VejpQP -->
                        <option value="" selected disabled>Etablissement scolaire</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-1">
                <svg>
                    <rect x="0" y="0" fill="none" width="100%" height="100%"/>
                </svg>
                Trouve ma Formation !
            </button>
        </form>
</div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="static/js/index.js"></script>
</body>
</html>