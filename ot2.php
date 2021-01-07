<?php
require_once "setup.php";
require_once "decrypt.php";

function oblivious_transfer2 ($uid, $order, $server, $session_id) {
    // key generation  
    $setup = setup();
    // public key
    $key = [
        'a' => htmlspecialchars($setup['a']),
        'b' => htmlspecialchars($setup['b']),
        'p' => htmlspecialchars($setup['p']),
        'r' => htmlspecialchars($setup['r']),
        'Y' => $setup['Y']
    ];
    
    // secret key
    $skey = [
        'x' => htmlspecialchars($setup['x']),
        'G' => $setup['G']
    ];

    // random int generation
    $rand = [];
    for($i=0; $i<2; $i++) {
        $rand[$i] = rand(1,100);
    }
    print_r($rand);
    
    // SP request
    $url = "http://10.229.71.229/api/cal_ot2.php?"
        ."returnOrigin=".$server
        ."&session_id=".$session_id
        ."&random0=".$rand[0]
        ."&random1=".$rand[1]
        ."&key_a=".$key['a']
        ."&key_b=".$key['b']
        ."&key_p=".$key['p']
        ."&key_r=".$key['r']
        ."&key_Y0=".$key['Y'][0]
        ."&key_Y1=".$key['Y'][1];
    
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
        //echo "OK<br>";
        $ret['result'] = $jsonArray['result'];
        $ret['q'] = $jsonArray['q'];
        $ret['x'] = $jsonArray['x'];
    }
    else {
        $ret = [];
        $ret['result'] = $jsonArray['result'];
        $ret['message'] = $jsonArray['message'];
    }

    // calculate y_i and c_i
    $yi = [];
    $j = 0;
    foreach ($rand as $r) {
        $yi[$j] = decrypt($ret['q'] - $r, $key['r'], $skey['x'], $skey['G'], $key['a'], $key['b'], $key['p']);
        $j++;
    }
    $ci = [];
    // order = [1:true, 2:false]
    if ($order == 1) {
        $ci[0] = $uid + $yi[0];  // idp1_user[n]
        $ci[1] = 0 + $yi[1];     // idp1_user0 ; dummyID
    }
    // order = [1:false, 2:true]
    else if ($order == 0) {
        $ci[0] = 0 + $yi[0];
        $ci[1] = $uid + $yi[1];
    }
    else {
        return "the order is invalid number<br>";
    }

    return $ci;
}