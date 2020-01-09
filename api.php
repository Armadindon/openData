<?php
include "paramBDD.info.php";
header('Content-Type: application/json');


if(!isset($_GET["type"])){
    http_response_code(403);
    exit(1);
}


$bdd = new PDO("mysql:host=".$host.";dbname=".$db,$user,$pwd);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$json = [
    "status" => "ok",
    "results" => []
];

switch ($_GET["type"]){

    case "incrementVisit":
        //on recrée la table si elle n'existe pas
        $bdd->query("CREATE TABLE IF NOT EXISTS opendata_popularity ( idEtablissement CHAR(8) PRIMARY KEY NOT NULL , visits INT NOT NULL );");
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



}