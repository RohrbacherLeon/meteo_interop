<?php

$opts = array('http' => array('proxy'=> 'tcp://www-cache.iutnc.univ-lorraine.fr:3128/','request_fulluri'=> true));
$context = stream_context_create($opts);

//Call API météo
$param_auth = 'ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgtuAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bPw83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGRSPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5VThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2';
$param_localisation = '48.67103,6.15083';
$url_meteo = "http://www.infoclimat.fr/public-api/gfs/xml?_ll=$param_localisation&_auth=$param_auth";
$meteo_data = simplexml_load_string(file_get_contents($url_meteo, false, $context));
if($meteo_data){
    //Print météo
} else {
    //Météo indisponible
}

//Print carte
//Call API parkings
$url_parkings = "http://www.velostanlib.fr/service/carto";
$parkings_data = simplexml_load_string(file_get_contents($url_parkings));
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
    } 
} else {
    //Parkings indisponibles
}