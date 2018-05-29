<?php

$caddress = $_REQUEST['caddress']; 

    $cprepAddr = str_replace(' ','+',$caddress);
    $cgeocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$cprepAddr.'&sensor=false&key=ENTERKEYHERE');
    $coutput= json_decode($cgeocode);
    $clatitude = $coutput->results[0]->geometry->location->lat;
    $clongitude = $coutput->results[0]->geometry->location->lng;
	
	


require ('../mysqli_connect.php'); // Connect to the db.


$origLat = $clatitude;
$origLon = $clongitude;
$dist = 25; // This is the maximum distance (in miles) away from $origLat, $origLon in which to search
$sql = "SELECT firstName, lastName, phone, latitude, longitude, round(3956 * 2 * 
          ASIN(SQRT( POWER(SIN(($origLat - latitude)*pi()/180/2),2)
          +COS($origLat*pi()/180 )*COS(latitude*pi()/180)
          *POWER(SIN(($origLon-longitude)*pi()/180/2),2))),2) 
          as distance FROM attOpenings WHERE 
          longitude between ($origLon-$dist/cos(radians($origLat))*69) 
          and ($origLon+$dist/cos(radians($origLat))*69) 
          and latitude between ($origLat-($dist/69)) 
          and ($origLat+($dist/69)) 
          having distance < $dist ORDER BY distance limit 100"; 