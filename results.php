<?php
//on récupère les écoles en fonctions des paramètres rentrés
if (!(isset($_POST["type"]) && isset($_POST["dep"]) && isset($_POST["year"]))){
    header('Location: index.php'); // on redirect si les paramètres ne sont pas remplis
}

$json = file_get_contents("https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=10000&sort=-rentree_lib&refine.rentree_lib=2017-18&refine.dep_ins_lib=".$_POST["dep"]."&refine.sect_disciplinaire_lib=".$_POST["type"]."&refine.niveau=".$_POST["year"]."&fields=etablissement,etablissement_lib,com_ins_lib,sect_disciplinaire_lib,diplome_lib,libelle_intitule_1");
$infos = json_decode($json,true);
$idEtablissements = array();
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
                        <th>Intitulé de la Formation</th>
                        <th>Plus D'Informations</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="tbl-content">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                    <?php
                    foreach($infos["records"] as $record){
                        if(!in_array($record["fields"]["etablissement"],$idEtablissements)){
                            array_push($idEtablissements,$record["fields"]["etablissement"]);
                        }
                        printf("<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a id='loc_%s'>Lien</a></td>
                    </tr>",
                            $record["fields"]["etablissement_lib"],
                            $record["fields"]["com_ins_lib"],
                            $record["fields"]["sect_disciplinaire_lib"],
                            $record["fields"]["diplome_lib"],
                            $record["fields"]["libelle_intitule_1"],
                            $record["fields"]["etablissement"]
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

    var map = L.map("map",{});
    var dict = {};

    L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
        maxZoom: 18
    }).addTo(map);

    <?php
    //on ajoute les marqueurs sur la carte
            //On récupère les coordonées du dernier point placé pour le mettre au centre au cas ou si l'utilisateur ne souhaite pas donner sa géolocalisation
            $lat = 0.0;
            $long = 0.0;
            foreach ($idEtablissements as $id){
                $etablissement = json_decode(file_get_contents("https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-etablissements-enseignement-superieur&sort=uo_lib&facet=uai&refine.uai=".$id."&fields=coordonnees,uo_lib,url"),true);
                $lat = $etablissement["records"][0]["fields"]["coordonnees"][0];
                $long = $etablissement["records"][0]["fields"]["coordonnees"][1];
                printf("
                L.marker([%f, %f]).addTo(map).bindPopup(\"<a href='%s'>%s</a>\");
                ",$lat,$long,$etablissement["records"][0]["fields"]["url"],$etablissement["records"][0]["fields"]["uo_lib"]);

                printf("document.getElementById('loc_%s').addEventListener('click',event=>{
                    map.setView([%f,%f],20);
                });",$id,$lat,$long);
            }

    ?>

    map.setView([<?php printf("%f, %f",$lat,$long);?>],10)

    if(navigator.geolocation) {
        console.log("Ok");
        navigator.geolocation.getCurrentPosition(position => {
            map.setView([position.coords.latitude,position.coords.longitude],10);
        });
    }




</script>
</body>


</html>