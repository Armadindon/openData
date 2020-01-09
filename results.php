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
    <?php
    if (count($infos["records"])>0) {
        ?>
        <h1>Voici les résultats !</h1>
        <div class="results">
            <div class="table">
                <div class="tbl-header">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <thead>
                        <tr>
                            <th>Ecole<img src="static/img/up-arrow.png" class="order" id="0"/></th>
                            <th>Ville<img src="static/img/up-arrow.png" class="order" id="1"/></th>
                            <th>Specialite<img src="static/img/up-arrow.png" class="order" id="2"/></th>
                            <th>Type de Diplôme<img src="static/img/up-arrow.png" class="order" id="3"/></th>
                            <th>Intitulé de la Formation <img src="static/img/up-arrow.png" class="order" id="4"/></th>
                            <th>Plus D'Informations</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="tbl-content">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tbody id="body-table"> <!-- est rempli par du JavaScript et non par le PHP (Permet de faire des actions de tri sans avoir a raffraichir la page -->


                        </tbody>
                    </table>
                </div>
                <div class="searchBar"><input type="search" id="searchInput" placeholder="Rechercher ..."><img src="static/img/search.png" id="searchImage"></div>

            </div>
            <div id="map">
            </div>
        </div>

        <h2>Une autre formation ?</h2>
        <h2><a href="index.php">Retourner a l'acceuil</a></h2>
        <?php
    }else{
        ?>
        <h1> Aucun Résultat trouvé !</h1>
        <h2><a href="index.php">Retourner a l'acceuil</a></h2>
    <?php
    }
    ?>
</div>
<script src="static/js/leaflet.js"></script>
<script type="text/javascript">

    function printResults(array){
        document.getElementById("body-table").innerHTML = "";
        array.forEach(etab =>{
        document.getElementById("body-table").innerHTML += "" +
            "<tr> <td>"+etab[0]+"</td> <td>"+etab[1]+"</td> <td>"+etab[2]+"</td> <td>"+etab[3]+"</td> <td>"+etab[4]+"</td> <td><a class='loc_"+etab[5]+"'>Lien</a></td> </tr>"
        });
    }

    function searchKeyWord(list,keyWord) {
        let results = [];
        for(let array of list){
            for(let value of array.slice(0,array.length-1)){
                if(value.toUpperCase().includes(keyWord.toUpperCase())){
                    results.push(array);
                    break;
                }
            }
        }
        return results;
    }



    var dataResults = [];

    <?php
            //on transmet les données au javascript comme ca le Javascript pourra faire ses actions sans a voir a raffraichir la page
    foreach ($infos["records"] as $record) {
        if (!in_array($record["fields"]["etablissement"], $idEtablissements)) {
            array_push($idEtablissements, $record["fields"]["etablissement"]);
        }
        printf("dataResults.push([\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"]);\n",
            $record["fields"]["etablissement_lib"],
            $record["fields"]["com_ins_lib"],
            $record["fields"]["sect_disciplinaire_lib"],
            $record["fields"]["diplome_lib"],
            $record["fields"]["libelle_intitule_1"],
            $record["fields"]["etablissement"]
        );
    }
    ?>

    printResults(dataResults);


    var map = L.map("map",{});
    var dict = new Map();


    L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
        maxZoom: 18
    }).addTo(map);



    function bindLink(map,dict){
        <?php
            echo ("let links;");
            //on ajoute les marqueurs sur la carte
            //On récupère les coordonées du dernier point placé pour le mettre au centre au cas ou si l'utilisateur ne souhaite pas donner sa géolocalisation
            $lat = 0.0;
            $long = 0.0;
            foreach ($idEtablissements as $id){
                $etablissement = json_decode(file_get_contents("https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-etablissements-enseignement-superieur&sort=uo_lib&facet=uai&refine.uai=".$id."&fields=coordonnees,uo_lib,url,adresse_uai,com_nom,code_postal_uai,numero_telephone_uai"),true);
                $lat = $etablissement["records"][0]["fields"]["coordonnees"][0];
                $long = $etablissement["records"][0]["fields"]["coordonnees"][1];
                printf("
                dict.set('%s',L.marker([%f, %f]).addTo(map).bindPopup(\"<b>%s</b><br/><a href='%s'>Site web</a><br/><b>Adresse :</b> %s (%s) - %s%s\"));
                ",$id,$lat,$long,$etablissement["records"][0]["fields"]["uo_lib"],$etablissement["records"][0]["fields"]["url"],$etablissement["records"][0]["fields"]["com_nom"],$etablissement["records"][0]["fields"]["code_postal_uai"],$etablissement["records"][0]["fields"]["adresse_uai"],(isset($etablissement["records"][0]["fields"]["numero_telephone_uai"])? "<br/><b>Téléphone :</b> ".$etablissement["records"][0]["fields"]["numero_telephone_uai"]:""));

                printf("
                    links = document.getElementsByClassName('loc_%s');
                    for (let i = 0;i<links.length;i++){
                        links.item(i).addEventListener('click',event=>{
                                map.closePopup();
                                map.setView([%f,%f],20);
                                dict.get('%s').openPopup();
                            });
                    }",$id,$lat,$long,$id);
            }


        ?>
    }

    bindLink(map,dict);


    map.setView([<?php printf("%f, %f",$lat,$long);?>],10)

    if(navigator.geolocation) {
        console.log("Ok");
        navigator.geolocation.getCurrentPosition(position => {
            map.setView([position.coords.latitude,position.coords.longitude],10);
        });
    }

        var cursors = document.getElementsByClassName("order");

    for (let i = 0;i<cursors.length;i++){
        cursors.item(i).addEventListener('click',event=>{
            //on reset les rotations
            for (let j = 0; j<cursors.length; j++){
                if(event.target.id !== cursors.item(j).id){
                    cursors.item(j).style.transform = "rotate(0deg)";
                }else{
                    cursors.item(j).style.transform = "rotate(180deg)";
                }
            }

            //on met en place le sort
            dataResults.sort((a,b)=>a[event.target.id].localeCompare(b[event.target.id]));
            //on réaffiche la liste
            printResults(dataResults);
            //on remet bien les marqueurs
            bindLink(map,dict);
        });
    }

    //TODO : Gérer la connexion Recherche / Order

    //pour la barre de recherche
    document.getElementById("searchImage").addEventListener("click",event=>{
        printResults(searchKeyWord(dataResults,document.getElementById("searchInput").value));
    });

    document.getElementById("searchInput").addEventListener("keyup",event=>{
        if(event.key === "Enter"){
            printResults(searchKeyWord(dataResults,document.getElementById("searchInput").value));
        }
    });






</script>
</body>


</html>