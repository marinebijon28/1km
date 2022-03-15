<?php
    function calculDistance($lat, $lng, $value, &$km) {
        $lngb = $value->lon * (M_PI / 180);
        $latb = $value->lat * (M_PI / 180);
        $r = 6372.797;
        $dlat = $latb - $lat;
        $dlng = $lngb - $lng;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat) * cos($latb) * sin(
        $dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;

        if ($km <= 1)
            return $value;
        return null;
    }
    
    function trouveStations($lat, $lng) {
        $json = file_get_contents('https://velib-metropole-opendata.smoove.pro/opendata/Velib_Metropole/station_information.json');
        $obj = json_decode($json);
        echo '<pre>';
        //print_r($obj->data->stations);
        echo '</pre>';
        $lat *= (M_PI / 180);
        $lng *= (M_PI / 180);
        $result = [];
        $km = null;
        foreach ($obj->data->stations as $key => $value)
        {
           // print_r($value->lat);
           // echo "lat : " . $value->lat . " lon : " .  $value->lon . '<br/>';
            if (($res = calculDistance($lat, $lng, $value, $km)) != null){
               $result[] = $res; 
               //$result[] = 
            }


         //   $arcos = (sin($lat) * sin($value->lat) + (cos($lat) * cos($value->lat) * cos($value->lon - $lng));
           // $km = 6371 * acos($arcos);
        



            // $kmx = ($value->lon - $lng) * cos(($lat + $value->lat) / 2);
            // $kmy = ($lat - $value->lat);
            // //echo $kmx . " " . $kmy . '<br/>';
            // $kmz = sqrt($kmx ** 2 + $kmy ** 2);
            //$km = (1852 * 60) * $kmz;
            //echo $km . '<br/>';
            //$long
        } 
        echo('<pre>');
        print_r($result);
        echo('</pre>');
        return $result;
    }

    echo "Les stations à moins de 1 km sont : <pre>";
    print_r(trouveStations(48.85, 2.38));
    echo "</pre>";

?>Ò