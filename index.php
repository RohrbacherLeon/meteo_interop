<?php
ini_set('display_errors', 1);
error_reporting(1);

$default_opts = array(
    'http'=>array(
        'proxy'=> 'tcp://www-cache.iutnc.univ-lorraine.fr:3128/',
        'request_fulluri'=> true
    )
  );

$default = stream_context_set_default($default_opts);


function parseHttpCode($http_response_header){
    return explode(' ', $http_response_header[0])[1];
}

function callApi(string $url, string $api){
    $results = file_get_contents($url);
    switch(parseHttpCode($http_response_header)){
        case "200":
        return [true,new SimpleXMLElement($results)];
        case '403':
        return [false, "L'accès à l'API $api est interdit."];
        case '404':
        return [false, "La ressource de l'API $api est introuvable."];
        case '500':
        return [false, "L'API $api ne répond pas."];
        default:
            return [false, "Une erreur par rapport à l'API $api est survenue."];
    }
}


/**
 * Calls the meteo API 
 * @param localisation 
 *      Localisation of the client to insert in url
 * @param auth 
 *      Authentification key to insert in url
 */
function callMeteo($localisation, $auth){
    $coords = explode(",",$localisation);
    $lat = $coords[0];
    $lng = $coords[1];
    $meteo_data = callApi("http://www.infoclimat.fr/public-api/gfs/xml?_ll=$lat,$lng&_auth=$auth", "Meteo");
    if($meteo_data[0]){
        $xsl = new DOMDocument();
        $xsl->load('meteo.xsl');
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xsl);
       
        $meteo = $proc->transformToXML($meteo_data[1]);
        return '
            <div class="meteo">
            '.$meteo.'
            </div>
        '; 
    } else {
        return "<div class='meteo'>$meteo_data[1]</div>";
    }
}

function getCoordinates(){
    $client_ip = $_SERVER["REMOTE_ADDR"];
    //$client_ip = "193.50.135.198";
    $coord = file_get_contents("https://ipapi.co/$client_ip/latlong/");
    return $coord;
}

function getVelos(){
    $url_velos = "http://www.velostanlib.fr/service/carto";
    $stations =  callApi("http://www.velostanlib.fr/service/carto", "des vélos");

        $coord = getCoordinates();
        $script = <<<EOT
            let map = L.map('mapid',{
                center : [$coord],
                zoom : 17
            });
    
            L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
                maxZoom: 25,
                id: 'mapbox.streets',
                accessToken: 'pk.eyJ1IjoiYW50aG9ueXppbmsiLCJhIjoiY2pwb2g2YXpkMDB6OTN4cWZvdTF3cGljZiJ9.ETkoyTeCMRTRX2SAc0TrXg'
            }).addTo(map);
    
            L.marker([$coord],{
                opacity : 1
            }).addTo(map);
    
            let icon = L.icon({
                iconUrl: 'assets/icon.png',
                iconSize: [38, 38],
            });
EOT;

    if($stations[0]){

        foreach ($stations[1]->markers->marker as $station) {
            $infos =  callApi("http://www.velostanlib.fr/service/stationdetails/nancy/".$station->attributes()->number, "des stations");
            if($infos[0]){
                $free = json_decode($infos[1]->free);
                $total = json_decode($infos[1]->total);
                $percentage = ($free/$total*100);
                $availability = availabilityColor($percentage);
                $lat = $station->attributes()->lat;
                $lng = $station->attributes()->lng;
                $name = substr($station->attributes()->name, 7);
                $script .= <<<END
                L.marker([$lat,$lng ],{
                    icon
                }).addTo(map).bindPopup("<h2>$name</h2><div class='availability_container'><div class='availability_level' style='background-color: rgb($availability); width: $percentage%'></div></div><h3>Vélos disponibles :$free/$total</h3>", {closeOnClick: true, autoClose: true});
END;
            }
        
        }
    }
    return $script;
}

function availabilityColor($number) {
    $number--;
    if ($number < 50) {
      $g = floor(255 * ($number / 50));
      $r = 255;
    } else {
      $g = 255;
      $r = floor(255 * ((50-$number%50) / 50));
    }
    return "$r,$g,0";
}



$param_auth = 'ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgtuAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bPw83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGRSPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5VThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2';

$html = "
<html>
		
    <head>
        <link rel='stylesheet' href='https://unpkg.com/leaflet@1.3.4/dist/leaflet.css' integrity='sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=='crossorigin=''/>
        <link rel='stylesheet' href='assets/style.css'>
        <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.6.1/css/all.css' integrity='sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP' crossorigin='anonymous'>
        <script src='https://unpkg.com/leaflet@1.3.4/dist/leaflet.js' integrity='sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA=='crossorigin=''></script>
    </head>
    <body>

        <div id='mapid' style='height:100vh;'>
            ".callMeteo(getCoordinates(),$param_auth)."
        </div>
        <script src='https://code.jquery.com/jquery-3.3.1.min.js'integrity='sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8='crossorigin='anonymous'></script>
        <script>
        ". getVelos() ."
        </script>
    </body>
    </html>
";

echo $html;
