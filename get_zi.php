<?php
require_once "crypt.php";
include("data/db_login.php");

// 財産比べプロトコルで用いる z_i を作成し，SP に渡して w_{ij} を得る
function get_zi ($attrs, $key, $rand, $server_name, $session_id, $uid) {
    $connection = mysqli_connect($db_host, 'root', 'admin', 'simplesaml');
    if (!$connection){
        die ("[error1] Could not connect to the database: <br />". mysqli_connect_error());
    }

    $z = [];
    $A = [];
    $i = 0;
    foreach ($attrs as $atr){
        // $atr の属性値を DB から取ってくる
        $query = "SELECT $atr FROM users WHERE uid = '$uid'";
        $result = mysqli_query($connection, $query);
        if(!$result) {
            die ("[error2] Could not query the database: <br />".mysqli_error());
        }
        else {
            $result_row = mysqli_fetch_row($result);
            $A[$i] = $result_row[0];
            $enc_r = encrypt($key['r'], $rand[$i], $key['Y'], $key['a'], $key['b'], $key['p']);
            $z[$i] = $enc_r - $A[$i];
            $i = $i+1;
        }
    }
    $url = "http://10.229.71.229/api/cal_wij.php?"
    ."returnOrigin=".$server_name
    ."&z=[";
    foreach ($z as $zi) {
        $url = $url.$zi.",";
    }
    $url = substr($url, 0, -1);
    $url = $url."]&session_id=".$session_id;

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
    $ret['result'] = $jsonArray['result'];
    $ret['hash_function'] = $jsonArray['hash_function'];
    $ret['algo'] = $jsonArray['algo'];
    $ret['key'] = $jsonArray['key'];
    $ret['w_ij'] = $jsonArray['w_ij'];
    $ret['random'] = $jsonArray['random'];
    $ret['session_id'] = $jsonArray['session_id'];
    $ret['A_i'] = $A;

    return $ret;
}
?>