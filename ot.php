<?php
require_once "crypt.php";

function oblivious_transfer ($key, $depth, $rand, $ci, $session_id, $server) {
    $i = 0;

    if ($depth !== count($ci)) {
        die ("the count of the attributes must be as many as the count of c_i");
    }
    else if ($ci[0] !== 0  && $ci[0] !== 1) {
        die ("c_i values must be 1 or 0");
    }
    else if (count($rand[0]) !== 2) {
        die ("the count of random int for c_1 must be 2");
    }

    $nextnode = 0;
    $ret = [];

    foreach ($rand as $rands) {
        $x = rand(1,100);

        if ($i == 0) {
            $index = $ci[0];
            $q = $rands[$index];
            //echo "0 : q = ".$q;
        }
        else {
            $n = $nextnode % 10;
            $index = 2 * $n - 2 + $ci[$i];
            $q = $rands[$index];
            //echo "$i : q = ".$q;
        }

        $enc = encrypt($key['r'], $x, $key['Y'], $key['a'], $key['b'], $key['p']);
        $q = $enc + $q;

        // SP問い合わせ
        $url = "http://10.229.71.229/api/cal_ot.php?"
        ."returnOrigin=".$server
        ."&session_id=".$session_id
        ."&count=".$i
        ."&q=".$q;

        $option = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 1000,
            CURLOPT_HTTPHEADER => array('Cookie: PHPSESSID='.$session_id),
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, $option);
        
        $json = curl_exec($ch);
        $info = curl_getinfo($ch);
        $errorNo = curl_errno($ch);

        curl_close($ch);

        if ($errorNo !== CURLE_OK) {
            return "CURL ERROR : ".$errorNo."<br>";
        }

        if ($info['http_code'] !== 200) {
            return "HTTP ERROR : ".$info['http_code']."<br>";
        }

        $jsonArray = json_decode($json, true);
        if(count($jsonArray) === 0) {
            return "CAUGHT ARRAY IS NULL<br>";
        }
        $ret = [];
        if ($jsonArray['result'] == 'OK') {
            $ret['result'] = $jsonArray['result'];
            $ret['c'] = $jsonArray['c'];
            $nextnode = $ret['c'][$index] - $x;
            $ret['nextnode'] = $nextnode;
        }
        else {
            $ret = [];
            $ret['result'] = $jsonArray['result'];
            $ret['message'] = $jsonArray['message'];
            $ret['debug'] = $jsonArray['debug'];
            break;
        }
        $i++;
    }

    return $ret;
}
?>