<?php

$json = file_get_contents("https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&sort=-rentree_lib&facet=dep_ins_lib&facet=sect_disciplinaire_lib&refine.rentree_lib=2017-18");
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
    <h1>Trouve ma Formation!</h1>
        <form action="results.php" method="post">
            <div class="buttons">
                <div class="select">
                    <select name="year" required> <!-- src : https://codepen.io/raubaca/pen/VejpQP -->
                        <option selected disabled>Niveau D'étude</option>
                        <?php
                        for($i = 0;$i<6;$i++){
                            printf("<option value=\"%d\">Bac+ %d</option>",$i+1,$i);
                        }
                        ?>
                    </select>
                </div>
                <div class="select" >
                    <select name="type" required> <!-- src : https://codepen.io/raubaca/pen/VejpQP -->
                        <option selected disabled>Type</option>
                        <?php
                        foreach ($infos["facet_groups"][1]["facets"] as $spec){
                            printf("<option value=\"%s\">%s</option>",$spec["name"],$spec["name"]);
                        }
                        ?>
                    </select>
                </div>
                <div class="select">
                    <select name="dep" required> <!-- src : https://codepen.io/raubaca/pen/VejpQP -->
                        <option selected disabled>Département</option>
                        <?php
                        foreach ($infos["facet_groups"][0]["facets"] as $dep){
                            printf("<option value=\"%s\">%s</option>",$dep["name"],$dep["name"]);
                        }
                        ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-1">
                <svg>
                    <rect x="0" y="0" fill="none" width="100%" height="100%"/>
                </svg>
                Trouve mon Alternance !
            </button>
        </form>
</div>
</body>
</html>