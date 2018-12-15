function generateMap(center){
    let map = L.map('mapid',{
        center,
        zoom : 17

    });
    
    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 25,
        id: 'mapbox.streets',
        accessToken: 'pk.eyJ1IjoiYW50aG9ueXppbmsiLCJhIjoiY2pwb2g2YXpkMDB6OTN4cWZvdTF3cGljZiJ9.ETkoyTeCMRTRX2SAc0TrXg'
    }).addTo(map);

    return map;
}


function parkings(map){
    $.get('http://www.velostanlib.fr/service/carto', function(data, status){
        $(data).find('marker').each(function(index, marker){
            //console.log(marker);
            
            let coordonnee = [marker.getAttribute("lat"), marker.getAttribute("lng")];
            let infos ;
            
            $.get(`http://www.velostanlib.fr/service/stationdetails/nancy/${marker.getAttribute("number")}`, function(data, status){
                
                
            }).then((result) => {
                infos = {
                    total : $(result).find("total").text(),
                    free  : $(result).find("free").text(),
                    name  : marker.getAttribute("name").split("-")[1]
                }

                html = `
                    <h2>${infos.name}</h2>
                    <p style="text-align:center">Vélos disponibles : ${infos.free} / ${infos.total}</p>
                `

                let icon = L.icon({
                    iconUrl: 'assets/icon.png',
                    iconSize: [38, 38],
                });

                L.marker(coordonnee,{
                    icon
                }).addTo(map).bindPopup(html, {closeOnClick: true, autoClose: true});
            }).catch((err) => {
                
            });
            
            

        });    
    })

    
}


function getLocalisation(data, status){
    let coordonneeUser = [data.latitude, data.longitude];
    let map = generateMap(coordonneeUser)
    
    L.marker(coordonneeUser,{
        opacity : 1
    }).addTo(map);

    parkings(map)
}



let url_ip = "http://api.ipstack.com/check?access_key=1c2c31a9132584a1c622b7e1bf032084";
$.ajax({
    url : url_ip,
    success : getLocalisation ,
    error : function(resultat, statut, erreur){
        console.log(resultat, statut, erreur);
        

    },
});


