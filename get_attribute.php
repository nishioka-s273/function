<?php
function get_attribute($server_name) {
    // IPアドレスで書かないとエラーになる
    $url = "http://10.229.71.229/api/attribute.php?"
    //$url = "http://sp1.local/api/attribute.php?"
    ."returnOrigin=".$server_name;

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
    $ret['result'] = $jsonArray['result'];
    $ret['attributes'] = $jsonArray['attributes'];
    $ret['key'] = $jsonArray['key'];
    $ret['session_id'] = $jsonArray['session_id'];

    return $ret;
}
//$res = get_attribute("idp1.local");
?>