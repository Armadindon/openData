<?php
include "paramWebsite.info.php";
header('Content-Type: application/json');


/*Notes paramWebsite.info.php:
 *Le fichier contient plusieurs variables qui sont intentionnellement gitIgnore:
 *  -$host qui est l'host de la base de données
 *  -$db qui est le nom de la base de donnée
 *  -$user qui est l'user de la base de donnée
 *  -$pwd le mot de passe de l'user
 */

/* NOTES POUR LE PROFESSEUR
 * Je n'ai pas crée de classes PHP car dans le cas de mon architecture, celle ci aurait eu aucune utilité
 * En effet, le code est très peu dupliqué (pas du tout)
 */

if(!isset($_GET["type"])){
    http_response_code(403);
    exit(1);
}


$bdd = new PDO("mysql:host=".$host.";dbname=".$db,$user,$pwd);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//on recrée la table si elle n'existe pas
$bdd->query("CREATE TABLE IF NOT EXISTS opendata_popularity ( idEtablissement CHAR(8) PRIMARY KEY NOT NULL , visits INT NOT NULL );");
$bdd->query("CREATE TABLE IF NOT EXISTS search_popularity ( idRecord VARCHAR(50) PRIMARY KEY NOT NULL , nbSearch INT NOT NULL );");

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

    case "incrementSearch":
        if(!isset($_GET["searched"])){
            http_response_code(500); // erreur dans la requête sql
            exit(1);
        }
        $searched = $_GET["searched"];
        $result = $bdd->query("INSERT INTO search_popularity VALUES ('".$searched."',1) ON DUPLICATE KEY UPDATE nbSearch=nbSearch+1;");
        echo json_encode($json);
        break;

    case "nbSearch":
        if(!isset($_GET["searched"])){
            http_response_code(500); // erreur dans la requête sql
            exit(1);
        }
        $searched = $_GET["searched"];
        $rq = $bdd->query("SELECT nbSearch FROM search_popularity WHERE idRecord='".$searched."';")->fetch(); // ne retourne qu'un résultat
        if($rq["nbSearch"] != null){
            $json["results"][0] = $rq["nbSearch"];
        }else{
            $json["results"][0] = 0;
        }
        echo json_encode($json);
        break;

    case "getInfo":
        $url = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=10000&sort=-rentree_lib&refine.rentree_lib=2017-18";

        if(isset($_GET["diplome_rgp"])) $url .= "&refine.diplome_rgp=".$_GET["diplome_rgp"];
        if(isset($_GET["sect_disciplinaire_lib"])) $url .= "&refine.sect_disciplinaire_lib=".$_GET["sect_disciplinaire_lib"];
        if(isset($_GET["reg_ins_lib"])) $url .= "&refine.reg_ins_lib=".$_GET["reg_ins_lib"];
        if(isset($_GET["dep_etab_lib"])) $url .= "&refine.dep_etab_lib=".$_GET["dep_etab_lib"];
        if(isset($_GET["etablissement_lib"])) $url .= "&refine.etablissement_lib=".$_GET["etablissement_lib"];
        $url .= "&fields=etablissement,etablissement_lib,com_ins_lib,sect_disciplinaire_lib,diplome_lib,libelle_intitule_1&apikey=".$api;
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