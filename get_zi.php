<?php
require "crypt.php";

// 財産比べプロトコルで用いる z_i を作成し，SP に渡して w_{ij} を得る
function get_zi ($attrs, $key, $rand, $server_name) {
    echo "<br>";
    print_r($key);
    $i = 0;
    foreach ($attrs as $atr){
        echo "<br>".$atr;

        // $atr の属性値を DB から取ってくる (とりあえずランダム整数で対処)
        $z[$i] = rand(1, 10);
        echo "<br> att".$i." = ".$z[$i];
        $enc_r = encrypt($key['r'], $rand[$i], $key['Y'], $key['a'], $key['b'], $key['p']);
        echo "<br> enc(r_".$i.") = ".$enc_r;
        $z[$i] = $enc_r - $z[$i];
        echo "<br> z_".$i." = ".$z[$i];
        $i = $i+1;
    }

    $url = "http://10.229.71.229/api/cal_wij.php?"
    ."returnOrigin=".$server_name
    ."&z=[";
    foreach ($z as $zi) {
        $url = $url.$zi.",";
    }
    $url = substr($url, 0, -1);
    $url = $url."]";

    $option = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 3,
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, $option);
    
    $json = curl_exec($ch);
    $info = curl_getinfo($ch);
    $errorNo = curl_errno($ch);

    curl_close($ch);

    if ($errorNo !== CURLE_OK) {
        echo "CURL ERROR : ".$errorNo."<br>";
    }

    if ($info['http_code'] !== 200) {
        echo "HTTP ERROR : ".$info['http_code']."<br>";
    }

    $jsonArray = json_decode($json, true);
    if(count($jsonArray) === 0) {
        echo "CAUGHT ARRAY IS NULL<br>";
    }

    $ret = [];
    $ret['zi'] = $jsonArray['zi'];

    return $ret;
}