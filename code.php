<?php
require_once "/var/www/function/get_attribute.php";
require_once "/var/www/function/get_zi.php";
require_once "/var/www/function/get_ci.php";
require_once "/var/www/function/ot.php";

// get attributes used to authorize an user, from the SP
$sp_attr = get_attribute("idp1.local");
$result = $sp_attr["result"];
if ($result !== "OK") {
    throw new Exception("Unable to get the attributes.");
}

$attr = $sp_attr["attributes"];
$sp_key = $sp_attr["key"];
$session_id = $sp_attr["session_id"];

// get the user's user ID
if (empty($attributes["uid"])) {
  throw new Exception("Missing uid attribute.");
}
$uid = $attributes["uid"][0];
// for debug
//$uid = "idp1_user1";

// random values for get_zi function
$i = 0;
$rand = [];
foreach($attr as $atr) {
    $rand[$i] = rand(1,100);
    $i = $i + 1;
}

// get the calculated w_{i}{j} values from the SP
$wijs = get_zi($attr, $sp_key, $rand, "idp1.local", $session_id, $uid);
if ($wijs['result'] !== 'OK') {
  throw new Exception ("Could not get the value w_ij from the SP");
}

$hash_function = $wijs['hash_function'];
$algo = $wijs['algo'];
$yao_key = $wijs['key'];
$wij = $wijs['w_ij'];
$random = $wijs['random']; // random int r_0, r_1, ..., for obious transfer
$A = $wijs['A_i'];

// calculate the c_{i} what is indicating the result of magic protocol (if A_{i} < T_{i} or not)
$ci = get_ci($hash_function, $algo, $yao_key, $wij, $A, $rand);

// ユーザIDを暗号化するための鍵
$uid_key = obious_transfer ($sp_key, count($attr), $random, $ci, $session_id, "idp1.local");
//print_r($uid_key);

$mail = $uid."@example.net";
$attributes["inter_values"] = array(
  "mail" => $mail,
  "debug" => $uid_key['nextnode'],
);