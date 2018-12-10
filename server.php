<?php

$opts = array('http' => array('proxy'=> 'tcp://127.0.0.1:8080', 'request_fulluri'=> true));
$context = stream_context_create($opts);

//Call API météo
$param_auth = '_auth=ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgt
uAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bP
w83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGR
SPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5V
ThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%
3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2';
$param_localisation = '_ll=48.67103,6.15083';
$url_meteo = "http://www.infoclimat.fr/public-api/gfs/xml?$param_localisation&$param_auth";
$velos = file_get_content($url_meteo);