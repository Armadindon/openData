<?php
//on récupère les écoles en fonctions des paramètres rentrés
if (!(isset($_POST["type"]) && isset($_POST["dep"]))){
    header('Location: index.php'); // on redirect si les paramètres ne sont pas remplis
}

$json = file_get_contents("https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=10000&sort=-rentree_lib&refine.rentree_lib=2017-18&refine.dep_ins_lib=".$_POST["dep"]."&refine.sect_disciplinaire_lib=".$_POST["type"]);
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
    <link rel="stylesheet" href="static/css/results.css">
    <link rel="stylesheet" href="static/css/leaflet.css">
    <link href="https://fonts.googleapis.com/css?family=Fredoka+One&display=swap" rel="stylesheet">

    <title>Résultats</title>
</head>
<body>


<div class="container">
    <h1>Voici les résultats !</h1>
    <div class="results">
        <div class="table">
            <div class="tbl-header">
                <table cellpadding="0" cellspacing="0" border="0">
                    <thead>
                    <tr>
                        <th>Ecole</th>
                        <th>Ville</th>
                        <th>Specialite</th>
                        <th>Type de Diplôme</th>
                        <th>Durée</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="tbl-content">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                    <?php
                    foreach($infos["records"] as $record){
                        printf("<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>5 ans</td>
                    </tr>",
                            $record["fields"]["etablissement_lib"],
                            $record["fields"]["com_ins_lib"],
                            $record["fields"]["sect_disciplinaire_lib"],
                            $record["fields"]["diplome_rgp"]
                        );
                    }
                    ?>

                    </tbody>
                </table>
            </div>
        </div>
        <div id="map">
        </div>
    </div>

    <h2>Une autre formation ?</h2>
    <h2><a href="index.php">Retourner a l'acceuil</a></h2>

</div>
<script src="static/js/leaflet.js"></script>
<script type="text/javascript">
    var map = L.map("map",{}).setView([51.505, -0.09], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
        attribution: '<a href="https://www.lije-creative.com">LIJE Creative</a>',
        maxZoom: 18
    }).addTo(map);
</script>
</body>


</html>