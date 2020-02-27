
function updateSelects(parameters) {
    let url = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&sort=-rentree_lib";
    $("#optional>div>select").each(function (index) {
        url += "&facet="+$(this).attr('name');
    });
    for (let key of Object.keys(parameters)){
        url += "&refine."+key+"="+parameters[key];
    }
    url += "&refine.rentree_lib=2017-18";
    $.get(url).done(function (data) {
        console.log(data);
        let alertBox = $("#alerts");

        if(data.hasOwnProperty("facet_groups")){
            alertBox.fadeOut();
            for(let item of data["facet_groups"]){
                let select = $("select[name='"+item["name"]+"']").not("#required>div>select");
                if(select != null){
                    select.children().not("option:disabled").remove();
                    for(let info of item["facets"]){
                        select.append("<option value=\""+info["name"]+"\">"+info["name"]+"</option>");
                    }
                }
            }
        }else{
            let select = $("#optional>div>select");
            if(select != null){
                select.children().not("option:disabled").remove();
            }
            alertBox.fadeIn("slow");//animation not working
        }

    });
}

var requestParamaters = {};

$("select").each(function (index) {
    $(this).val("");
});

$("#required>div>select, #optional>div>select").change(function () {
    if($(this).prop('disabled')){
        delete requestParamaters[$(this).attr('name')];
    }else{
        requestParamaters[$(this).attr('name')] = $(this).val();
    }
    updateSelects(requestParamaters);
});