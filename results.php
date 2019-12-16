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
                    <tr>
                        <td>EFREI</td>
                        <td>Villejuif</td>
                        <td>Informatique</td>
                        <td>Diplôme d'ingénieur</td>
                        <td>5 ans</td>
                    </tr>
                    <tr>
                        <td>UPEM</td>
                        <td>Marne la Vallée</td>
                        <td>Informatique</td>
                        <td>DUT</td>
                        <td>2 ans</td>
                    </tr>
                    <tr>
                        <td>UPEM</td>
                        <td>Marne la Vallée</td>
                        <td>Multimédia</td>
                        <td>DUT</td>
                        <td>2 ans</td>
                    </tr>

                    <tr>
                        <td>UPEM</td>
                        <td>Marne la Vallée</td>
                        <td>Multimédia</td>
                        <td>License</td>
                        <td>2 ans</td>
                    </tr>

                    <tr>
                        <td>UPEM</td>
                        <td>Marne la Vallée</td>
                        <td>Multimédia</td>
                        <td>License</td>
                        <td>2 ans</td>
                    </tr>

                    <tr>
                        <td>UPEM</td>
                        <td>Marne la Vallée</td>
                        <td>Multimédia</td>
                        <td>License</td>
                        <td>2 ans</td>
                    </tr>

                    <tr>
                        <td>UPEM</td>
                        <td>Marne la Vallée</td>
                        <td>Multimédia</td>
                        <td>License</td>
                        <td>2 ans</td>
                    </tr>

                    <tr>
                        <td>UPEM</td>
                        <td>Marne la Vallée</td>
                        <td>Multimédia</td>
                        <td>License</td>
                        <td>2 ans</td>
                    </tr>

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
    L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
        attribution: '<a href="https://www.lije-creative.com">LIJE Creative</a>',
        maxZoom: 18
    }).addTo(map);
</script>
</body>


</html>