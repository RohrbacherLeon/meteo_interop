<?php
ini_set('display_errors', 1);

/*
$default_opts = array(
    'http'=>array(
        'proxy'=> 'tcp://www-cache.iutnc.univ-lorraine.fr:3128/',
        'request_fulluri'=> true
    )
  );

$default = stream_context_set_default($default_opts);
*/


/**
 * Calls the meteo API 
 * @param localisation 
 *      Localisation of the client to insert in url
 * @param auth 
 *      Authentification key to insert in url
 * @return 
 *      True : xml file has been created with success
 *      False : API didn't respond
 */
function callMeteo($localisation, $auth){
    $lat = $localisation[1];
    $lng = $localisation[0];
    $url_meteo = "http://www.infoclimat.fr/public-api/gfs/xml?_ll=$lat,$lng&_auth=$auth";
    $meteo_data = new SimpleXMLElement(file_get_contents($url_meteo, false));
    if($meteo_data){
        $xsl = new DOMDocument();
        $xsl->load('meteo.xsl');
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xsl);
       
        $meteo = $proc->transformToXML($meteo_data);
        return '
            <div class="meteo">
            '.$meteo.'
            </div>
        '; 
    } else {
        return "<div class='meteo'>Pas de meteo :'(</div>";
    }
}

function getIp(){
    $url_ip = "http://api.ipstack.com/check?access_key=1c2c31a9132584a1c622b7e1bf032084";
    $ip_data =  json_decode(file_get_contents($url_ip, false));
    return [$ip_data->longitude, $ip_data->latitude];
}

function getVelos(){
    $url_velos = "http://www.velostanlib.fr/service/carto";
    $stations =  new SimpleXMLElement(file_get_contents($url_velos));

    $script = "";
    $html = "";
    foreach ($stations->markers->marker as $station) {
        $infos =  new SimpleXMLElement(file_get_contents("http://www.velostanlib.fr/service/stationdetails/nancy/".$station->attributes()->number));
        $free = json_decode($infos->free);
        $total = json_decode($infos->total);
        $lat = $station->attributes()->lat;
        $lng = $station->attributes()->lng;
        $name = $station->attributes()->name;
        $script .= <<<END
        L.marker([$lat,$lng ],{
            icon
        }).addTo(map).bindPopup("<h2>$name</h2><p style='text-align:center'>Vélos disponibles :$free/$total</p>", {closeOnClick: true, autoClose: true});
END;
    
    }
    return $script;
}

$param_auth = 'ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgtuAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bPw83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGRSPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5VThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2';

$script = "
    let map = L.map('mapid',{
        center : [".getIp()[1].", ".getIp()[0]."],
        zoom : 17
    });

    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: \"Map data &copy; <a href='https://www.openstreetmap.org/'>OpenStreetMap</a> contributors, <a href='https://creativecommons.org/licenses/by-sa/2.0/'>CC-BY-SA</a>, Imagery © <a href='https://www.mapbox.com/'>Mapbox</a>\",
        maxZoom: 25,
        id: 'mapbox.streets',
        accessToken: 'pk.eyJ1IjoiYW50aG9ueXppbmsiLCJhIjoiY2pwb2g2YXpkMDB6OTN4cWZvdTF3cGljZiJ9.ETkoyTeCMRTRX2SAc0TrXg'
    }).addTo(map);

    L.marker([".getIp()[1].", ".getIp()[0]."],{
        opacity : 1
    }).addTo(map);

    let icon = L.icon({
        iconUrl: 'assets/icon.png',
        iconSize: [38, 38],
    });

    ".getVelos()."
";


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
            ".callMeteo(getIp(),$param_auth)."
        </div>
        <script src='https://code.jquery.com/jquery-3.3.1.min.js'integrity='sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8='crossorigin='anonymous'></script>
        <script>
        ". $script ."
        </script>
    </body>
    </html>
";

echo $html;
