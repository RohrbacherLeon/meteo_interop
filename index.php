<?php
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
    $opts = array('http' => array(/*'proxy'=> 'tcp://www-cache.iutnc.univ-lorraine.fr:3128/',*/'request_fulluri'=> true));

    $context = stream_context_create($opts);
    $success = false;
    $url_meteo = "http://www.infoclimat.fr/public-api/gfs/xml?_ll=$localisation&_auth=$auth";
    $meteo_data = file_get_contents($url_meteo, false, $context);
    if($meteo_data){
        
        
        $xml_file = fopen('meteo.xml', 'w');
        fwrite($xml_file, $meteo_data);
        fclose($xml_file);
        $xml = new DOMDocument();
        $xml->load('meteo.xml');

        $xsl = new DOMDocument();
        $xsl->load('meteo.xsl');
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xsl);
       
        $meteo = $proc->transformToXML($xml);
        return '
            <div class="meteo">
            '.$meteo.'
            </div>
        '; 
    } else {
        var_dump('aucune donnée meteo');die;
    }
}

/**
 * Calls the IP localisation API 
 * @param ip 
 *      IP of the client to insert in url
 * @return 
 *      True : xml file has been created with success
 *      False : API didn't respond
 */
function callLocalisation($ip){
    
    $coordinates = '';
    $opts = array('http' => array(/*'proxy'=> 'tcp://www-cache.iutnc.univ-lorraine.fr:3128/',*/'request_fulluri'=> true));

    $context = stream_context_create($opts);
    $success = false;
    $url_ipapi = "https://ipapi.co/$ip/latlong/";
    $ipapi_data = file_get_contents($url_ipapi, false, $context);
    if($ipapi_data){    
        
        $coordinates = $ipapi_data;
    }
    else {
    var_dump('erreur données localisation');die;
}


    return $coordinates;
}


$param_auth = 'ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgtuAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bPw83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGRSPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5VThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2';
//

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
            ".callMeteo(callLocalisation('176.145.233.91'),$param_auth)."
        </div>

        <script src='https://code.jquery.com/jquery-3.3.1.min.js'integrity='sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8='crossorigin='anonymous'></script>
        <script src='assets/map.js'></script>
    </body>
    </html>
";

echo $html;
