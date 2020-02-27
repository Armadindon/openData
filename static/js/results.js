
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
        let req = $.ajax({
            url : "api.php",
            type : "GET",
            data: "type=getInfoSchool&school="+school,
            dataType: "json",
        });
        req.fail(function () {
            error =true;
        });

        req.done(function (msg) {
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

var error = false;
var lstSchools = new Set();
var dataResults = [];
var infoSchools = new Map();
var visitsBySchools = new Map();
var map = L.map("map",{});
var popups = new Map();

let urlInitialQuery = "type=getInfo";
for(let key of Object.keys(paramQuerry)){
    urlInitialQuery += "&"+key+"="+paramQuerry[key];
}

map.setView([48.856614,2.3522219],5); //au cas ou aucune formation n'a de coordonnées

console.log(urlInitialQuery);

    let req = $.ajax({
        url : "api.php",
        type : "GET",
        data: urlInitialQuery,
        dataType: "json",
    });
    req.fail(function () {
        error = true;
    });
    req.done(function (msg) {
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
    if(navigator.geolocation) {//Ne marche pas sur liflux
        navigator.geolocation.getCurrentPosition(function (position) {
            console.log(position);
            map.setView([position.coords.latitude,position.coords.longitude],10);
        });
    }
    if (error){
        alert("Il y a eu une erreur ! Merci de raffraîchir la Page");
        $(".loader").remove();
        $("#loader").append("<h1 style='color: #D81B60;'>Une erreur est survenue ! Merci de raffraîchir la page !</h1>")
    } else{
        printResults(dataResults,visitsBySchools);
        generatePopups(map,popups,infoSchools);
        bindLink(map,popups,visitsBySchools.keys());
        $("#loader").remove();
        $(this).unbind("ajaxStop");
    }
});