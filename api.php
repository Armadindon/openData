<?php
include "paramBDD.info.php";
header('Content-Type: application/json');


if(!isset($_GET["type"])){
    http_response_code(403);
    exit(1);
}

echo json_encode([
    "firstParamater"=>"mysql:host='".$host."';dbname='".$db."'",
    "user" => $user,
    "pwd" => $pwd
    ]);
//exit(0);
$bdd = new PDO("mysql:host=".$host.";dbname=".$db,$user,$pwd);


switch ($_GET["type"]){

    case "incrementVisit":
        //on recrée la table si elle n'existe pas
        $bdd->query("CREATE TABLE IF NOT EXISTS `bperrin_db`.`opendata_popularity` ( `idEtablissement` CHAR(8) NOT NULL , `visits` INT NOT NULL , PRIMARY KEY (`idEtablissement`)) ENGINE = InnoDB;");
        if(!isset($_GET["school"])){
            http_response_code(500); // erreur dans la requête sql
            exit(1);
        }
        $school = $_GET["school"];
        $bdd->query("INSERT INTO 'opendata_popularity' VALUES (".$school.",1) ON DUPLICATE KEY UPDATE visits=visits+1;");
        echo json_encode([
            "status" => "ok",
            "results" => []
        ]);
        break;

}