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
    <?php
    if (count($infos["records"])>0) {
        ?>
        <h1>Voici les résultats !</h1>
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
                            <th>Nombre de visites </th>
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
        <?php
    }else{
        ?>
        <h1> Aucun Résultat trouvé !</h1>
        <h2><a href="index.php">Retourner a l'acceuil</a></h2>
    <?php
    }
    ?>
</div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="static/js/leaflet.js"></script>
<script>
    
    function updateNbVisits(visitsBySchool) {
        for(let school of visitsBySchool.keys()){
            $.ajax({
                url : "api.php",
                type : "GET",
                data: "type=nbVisits&school="+school,
                dataType: "json"
            }).done(function (msg) {
                visitsBySchool.set(school,msg["results"][0]);
            })
        }
    }
    
    function generatePopups(map,dict,infosSchools) {
        for(let school of infosSchools.keys()){
            if(infosSchools.get(school) == null){
                dict.set(school,null);
            }else{
                let infos = infosSchools.get(school);
                let html = "<b>"+infos[1]+"</b><br/><a href='"+infos[2]+"'>Site web</a><br/><b>Adresse :</b> "+infos[3]+" ("+infos[4]+") - "+infos[5];
                if(infos[6] !== undefined) html+= "<br/><b>Numéro de téléphone : <b/>"+infos[6];
                dict.set(school,L.marker(infos[0]).addTo(map).bindPopup(html));
                map.setView(infos[0],10);
            }
        }
    }

    function printResults(array,visitsBySchool){
        $(".tbl-content>table>tbody").empty();
        for(let i =0;i<array.length;i++){
            let etab = array[i];
            document.getElementById("body-table").innerHTML += "<tr> <td>"+etab[0]+"</td> <td>"+etab[1]+"</td> <td>"+etab[2]+"</td> <td>"+etab[3]+"</td> <td>"+etab[4]+"</td> <td class='visits_"+etab[5]+"'>"+visitsBySchool.get(etab[5])+"</td> <td><a class='loc_"+etab[5]+"'>Lien</a></td> </tr>"
        }
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

    function bindLink(map,dict,lstSchools) {
        for (let school of lstSchools) {

            let links = document.getElementsByClassName("loc_" + school);
            let popup = dict.get(school);
            if (popup !== null) {
                for (let items of links) {
                    items.addEventListener('click', event => {
                        map.closePopup();
                        map.setView(popup.getLatLng(), 20);
                        popup.openPopup();
                        $.get("api.php?type=incrementVisit&school="+school);
                        $(".visits_"+school).each(function (index) {
                            let nb = parseInt($(this).html());
                            nb+=1;
                            $(this).html(""+nb);
                        });
                    });
                }
            } else {
                for (let items of links) {
                    items.classList.add('disabled');
                }
            }
        }
    }

    //lattitude et longitude, nom, site, ville, code posta, adresse, et éventuellement numéro de téléphone
    //Renvoie une map avec tous les infos sur toutes les écoles
    function getInfosOnSchools(lstSchools){
        let infos = new Map();
        for(let school of lstSchools){
            $.ajax({
                url : "api.php",
                type : "GET",
                data: "type=getInfoSchool&school="+school,
                dataType: "json",
            }).done(function (msg) {
                if(msg["nhits"] === 0){
                    infos.set(school,null);
                }else{
                    let result = msg["records"][0]["fields"];
                    infos.set(school,[result["coordonnees"],result["uo_lib"],result["url"],result["com_nom"],result["code_postal_uai"],result["adresse_uai"],result["numero_telephone_uai"]]);
                }
            });
        }
        return infos;
    }


    <?php
            printf("var dep = '%s';
                           var typeF = '%s';
                           var year = '%s';"
            ,$_POST["dep"],$_POST["type"],$_POST["year"]);
    ?>

    var lstSchools = new Set();
    var dataResults = [];
    var infoSchools = new Map();
    var visitsBySchools = new Map();
    var map = L.map("map",{});
    var popups = new Map();


    $.ajax({
        url : "api.php",
        type : "GET",
        data: "type=getInfo&typeF="+typeF+"&dep="+dep+"&year="+year,
        dataType: "json",
    }).done(function (msg) {
        for (let i =0;i<msg["records"].length;i++){
            let fields = msg["records"][i]["fields"];
            lstSchools.add(fields["etablissement"]);
            dataResults.push([fields["etablissement_lib"],fields["com_ins_lib"],fields["sect_disciplinaire_lib"],fields["diplome_lib"],fields["libelle_intitule_1"],fields["etablissement"]]);
            visitsBySchools.set(fields["etablissement"],0);
        }
        updateNbVisits(visitsBySchools);
        infoSchools = getInfosOnSchools(Array.from(lstSchools));
    });



    L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
        maxZoom: 18
    }).addTo(map);



    if(navigator.geolocation) {
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
            printResults(dataResults,visitsBySchools);
            //on remet bien les marqueurs
            bindLink(map,popups,visitsBySchools.keys());
        });
    }

    //TODO : Gérer la connexion Recherche / Order

    //pour la barre de recherche
    document.getElementById("searchImage").addEventListener("click",event=>{
        printResults(searchKeyWord(dataResults,document.getElementById("searchInput").value),visitsBySchools);
    });

    document.getElementById("searchInput").addEventListener("keyup",event=>{
        if(event.key === "Enter"){
            printResults(searchKeyWord(dataResults,document.getElementById("searchInput").value),visitsBySchools);
        }
    });


    $(document).ajaxStop(function () {
        printResults(dataResults,visitsBySchools);
        generatePopups(map,popups,infoSchools);
        bindLink(map,popups,visitsBySchools.keys());
        $("#loader").remove();
        $(this).unbind("ajaxStop");
    });
</script>
</body>


</html>