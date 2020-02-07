<?php
include "paramBDD.info.php";
header('Content-Type: application/json');


if(!isset($_GET["type"])){
    http_response_code(403);
    exit(1);
}


$bdd = new PDO("mysql:host=".$host.";dbname=".$db,$user,$pwd);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//on recrée la table si elle n'existe pas
$bdd->query("CREATE TABLE IF NOT EXISTS opendata_popularity ( idEtablissement CHAR(8) PRIMARY KEY NOT NULL , visits INT NOT NULL );");

$json = [
    "status" => "ok",
    "results" => []
];

switch ($_GET["type"]){

    case "incrementVisit":
        if(!isset($_GET["school"])){
            http_response_code(500); // erreur dans la requête sql
            exit(1);
        }
        $school = $_GET["school"];
        $bdd->query("INSERT INTO opendata_popularity VALUES ('".$school."',1) ON DUPLICATE KEY UPDATE visits=visits+1;");
        echo json_encode($json);
        break;

    case "nbVisits":
        if(!isset($_GET["school"])){
            http_response_code(500); // erreur dans la requête sql
            exit(1);
        }
        $school = $_GET["school"];
        $rq = $bdd->query("SELECT visits FROM opendata_popularity WHERE idEtablissement='".$school."';")->fetch(); // ne retourne qu'un résultat
        if($rq["visits"] != null){
            $json["results"][0] = $rq["visits"];
        }else{
            $json["results"][0] = 0;
        }
        echo json_encode($json);
        break;

    case "getInfo":
        $url = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=10000&sort=-rentree_lib&refine.rentree_lib=2017-18";

        if(isset($_GET["typeF"])) $url .= "&refine.sect_disciplinaire_lib=".$_GET["typeF"];
        if(isset($_GET["dep"])) $url .= "&refine.dep_ins_lib=".$_GET["dep"];
        if(isset($_GET["year"])) $url .= "&refine.niveau=".$_GET["year"];
        $url .= "&fields=etablissement,etablissement_lib,com_ins_lib,sect_disciplinaire_lib,diplome_lib,libelle_intitule_1";
        echo file_get_contents($url);
        break;

    case "getInfoSchool":
        if(!isset($_GET["school"])){
            http_response_code(500); // erreur dans la requête sql
            exit(1);
        }
        $url = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-etablissements-enseignement-superieur&sort=uo_lib&facet=uai&refine.uai=".$_GET["school"]."&fields=coordonnees,uo_lib,url,adresse_uai,com_nom,code_postal_uai,numero_telephone_uai";

        echo file_get_contents($url);
        break;



}