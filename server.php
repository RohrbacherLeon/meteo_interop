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
        
        /*$args = ['/_xml' => $xml];
        $xsltp = xslt_create();
        xslt_set_encoding($xsltp, 'UTF-8');
        $html = xslt_process($xsltp, 'arg:/_xml', './meteo.xsl', null, $args);*/
        $xml_file = fopen('meteo.xml', 'w');
        fwrite($xml_file, $meteo_data);
        $xml = new DOMDocument();
        $xml->load('meteo.xml');

        $xsl = new DOMDocument();
        $xsl->load('meteo.xsl');
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xsl);
       
        $meteo = $proc->transformToXML($xml);

        echo '
        <html>
		
        <head>
            <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.0/css/all.css" integrity="sha384-aOkxzJ5uQz7WBObEZcHvV5JvRW3TUc2rNPA7pe3AwnsUohiw1Vj2Rgx2KSOkF5+h" crossorigin="anonymous"/>
        </head>
        <body>
            <div class="meteo>
            '.$meteo.'
            </div>
        </body>
		</html>
        '; 
        die;
        $success = true;
    } else {
        $success = false;
    }
    return $success;
}



function parkings(){
    //Call API parkings
    $url_parkings = "http://www.velostanlib.fr/service/carto";
    $parkings_data = simplexml_load_string(file_get_contents($url_parkings));

    //les marqueurs
    $markers = $parkings_data->markers 
    if($parkings_data){
        //Print parkings
        //Call API vélo
        $parking_id = 1;
        $url_velos = "http://www.velostanlib.fr/service/stationdetails/nancy/$parking_id";
        $velos_data = simplexml_load_string(file_get_contents($url_velos));
        if($velos_data){
            //Places libres
            //Velos disponibles
            var_dump($velos_data);
        } else {
            //Infos velos indisponibles
            var_dump('velo indisponible');
        } 
    } else {
        //Parkings indisponibles
        var_dump('parkings indisponibles');
    }
}

$param_localisation = '48.67103,6.15083';

$param_auth = 'ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgtuAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bPw83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGRSPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5VThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2';
callMeteo($param_localisation, $param_auth);
//parkings();

