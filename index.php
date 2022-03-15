<?php


    function calculDistanceHaversine($lat, $lng, $value, &$km) {
        // transform in radiant == °
        $lngb = $value->lon * (M_PI / 180);
        $latb = $value->lat * (M_PI / 180);
        // rayon of planet
        $r = 6372.797;
        // calcul b - a
        $dlat = $latb - $lat;
        $dlng = $lngb - $lng;
        // arctan
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat) * cos($latb) * sin(
        $dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        // distance
        $km = $r * $c;

        if ($km <= 1)
            return $value;
        return null;
    }

    function sortTabTrieFusion(&$result) {
        for ($i = 0; $i < count($result); $i++) {
            $tmpmin = $i;
            for ($j = $i; $j < count($result); $j++) {
                if ($result[$tmpmin]->km > $result[$j]->km)
                {
                    $tmpmin = $j;
                }
            }
            $tmpmax = $result[$tmpmin];
            $result[$tmpmin] = $result[$i];
            $result[$i] = $tmpmax;
        }
      //  print_r($result);
    }
    
    function trouveStations($lat, $lng) {
        $json = file_get_contents('https://velib-metropole-opendata.smoove.pro/opendata/Velib_Metropole/station_information.json');
        $obj = json_decode($json);
        echo '<pre>';
     //   print_r($obj->data->stations);
        echo '</pre>';
        $lat *= (M_PI / 180);
        $lng *= (M_PI / 180);
        $result = [];
        $km = null;
        $i = 0;
        foreach ($obj->data->stations as $key => $value)
        {
           // print_r($value->lat);
           // echo "lat : " . $value->lat . " lon : " .  $value->lon . '<br/>';
            if (($res = calculDistanceHaversine($lat, $lng, $value, $km)) != null){
               $result[$i] = $res; 
               $result[$i]->km = $km;
               $i++;
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
        sortTabTrieFusion($result);

        // stations_id get nv api pour entrer une nouvelle cle avec le numéro de velo 

        echo('<pre>');
        print_r($result);
        echo('</pre>');
        return $result;
    }

    echo "Les stations à moins de 1 km sont : <pre>";
    print_r(trouveStations(48.85, 2.38));
    echo "</pre>";

?>Ò