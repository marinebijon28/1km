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

    function calculDistanceLoiDesSinus($lat, $lng, $value, &$km) {
        // transform in radiant == °
        $lngb = $value->lon * (M_PI / 180);
        $latb = $value->lat * (M_PI / 180);



        $arcos = (sin($lat) * sin($latb) + (cos($lat) * cos($latb) * cos($lngb - $lng)));
        $km = 6371 * acos($arcos);
        if ($km <= 1)
            return $value;
        return null;
    }

    function calculDistancePythagore($lat, $lng, $value, &$km) {
         // transform in radiant == °
         $lngb = $value->lon * (M_PI / 180);
         $latb = $value->lat * (M_PI / 180);
 
        $kmx = ($lngb - $lng) * cos($lat + $latb / 2);
        $kmy = ($latb - $lat);
        $kmz = sqrt((($kmx ** 2) + ($kmy ** 2)));
        $km = 1.852 * 60 * $kmz;
          
        if ($km <= 1)
            return $value;
        return null;
    }

    function sortTabTrieSelection(&$result) {
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
    }

    function sortTabTrieBubble(&$result) {
        for ($i = 0; $i < count($result); $i++) {
            $bool = false;
            for ($j = 0; $j + 1 < count($result); $j++) {
                if ($result[$j]->km > $result[$j + 1]->km) {
                    $bool = true;
                    $tmp = $result[$j];
                    $result[$j] = $result[$j + 1];
                    $result[$j + 1] = $tmp;
                }
            }
            if ($bool == false) {
                break;
            }
        }
    }

    function sortTabTrieInsertion(&$result) {
        for ($i = 0; $i < count($result); $i++) {
            $j = $i;
            $tmp = $result[$i];
            while ($j > 0 && $result[$j - 1]->km > $tmp->km) {
                $result[$j] = $result[$j - 1];
                $j--;
            }
           $result[$j] = $tmp;
        }

    }

    function tamiser(&$arbre, $noeud, $n) {
        // 22
        $k = $noeud;
        // 44
        $j = 2 * $k;

        echo "n : " . $n . '<br/>';
        // n = 44
        while ($j <= $n)
        {
            // j : 3 n : 21
            if ($j <= $n && $arbre[$j]->km < $arbre[$j + 1]->km) {
                $j++;
                echo "premier";
            }
            if ($arbre[$k]->km > $arbre[$j]->km) {
                echo "j : " . $j . " n :" . $n . "k :" . $k . '<br/>';
                echo "arbre k : " . $arbre[$k]->km . " arbre j :" . $arbre[$j]->km . '<br/>';;
                $tmp = $arbre[$k];
                $arbre[$k] = $arbre[$j];
                $arbre[$j] = $tmp;
                $k = $j;
                $j = 2 * $k;
                echo "deuxieme";
            }
            else {
                $j = $n + 1;
                echo "troisieme";
            }
        }
    }

    function sortTabTrieParTas(&$result) {
        $i = count($result) / 2;
        echo "i : " . $i % 2;
       if ($i % 2 == 0)
          tamiser($result, $i, count($result));
       else if ($i % 2 == 1)
       {
            $tmp = $result[$i];
            $result[$i] = $result[1];
            $result[1] = $tmp;
            tamiser($result, 1, $i - 1);
        }
    }

    function numberVelo($objvelo, $result) {
        foreach ($objvelo->data->stations as $key => $value) {
            for ($i = 0; $i < count($result); $i++){
                if ($result[$i]->station_id == $value->station_id)
                    $result[$i]->number_velo = $value->num_bikes_available;  
            }
        }        
        return $result;
    }
    
    function trouveStations($lat, $lng) {
        $json = file_get_contents('https://velib-metropole-opendata.smoove.pro/opendata/Velib_Metropole/station_information.json');
        $obj = json_decode($json);
      
        $lat *= (M_PI / 180);
        $lng *= (M_PI / 180);
        $result = [];
        $km = null;
        $i = 0;
        foreach ($obj->data->stations as $key => $value)
        {
            if (($res = calculDistanceHaversine($lat, $lng, $value, $km)) != null){
               $result[$i] = $res; 
               $result[$i]->km = $km;
               $i++;
            }
        } 

        sortTabTrieParTas($result);

        $json = file_get_contents('https://velib-metropole-opendata.smoove.pro/opendata/Velib_Metropole/station_status.json');
        $objvelo = json_decode($json);

       $result = numberVelo($objvelo, $result);

        return $result;
    }

    echo "Les stations à moins de 1 km sont : <pre>";
    print_r(trouveStations(48.85, 2.38));
    echo "</pre>";

?>