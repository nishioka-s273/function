<?php
//session_start();
$sid = $_COOKIE['SimpleSAML'];

require_once "/var/www/function/get_attribute.php";
require_once "/var/www/function/get_zi.php";
require_once "/var/www/function/get_ci.php";
require_once "/var/www/function/ot.php";
require_once "/var/www/function/ot2.php";
require_once "/var/www/function/crypt.php";

// get attributes used to authorize an user, from the SP
$sp_attr = get_attribute($sid, "idp1.local");
if ($sp_attr["result"] !== "OK") {
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
$zi = $wijs['zi'];

// calculate the c_{i} what is indicating the result of magic protocol (if A_{i} < T_{i} or not)
$ci = get_ci($hash_function, $algo, $yao_key, $wij, $A, $rand);

// ユーザIDを暗号化するための鍵
$uid_key = oblivious_transfer ($sp_key, count($attr), $random, $ci, $session_id, "idp1.local");

$order = $uid_key["nextnode"];

$uidnum = substr($uid, 9);

// 返却値 user ID
$retuid = oblivious_transfer2 ($uidnum, $order, "idp1.local", $session_id);

$attributes = array (
  "attr" => $attr,
  "session_id" => [$sid, $session_id],
  "attr_value" => $A,
  "sp_pubkey" => array(
    $sp_key["a"],
    $sp_key["b"],
    $sp_key["p"],
    $sp_key["r"],
    $sp_key['Y'][0],
    $sp_key['Y'][1],
  ),
  "enc_r" => array(
    $result_code,
    $output[0],
  ),
  "random_for_zi" => $rand,
  "zi" => $zi,
  "w0j" => $wij[0],
  "w1j" => $wij[1],
  "w2j" => $wij[2],
  "w3j" => $wij[3],
  "w4j" => $wij[4],
  "ci" => $ci,
  "rand_ot0" => $random[0],
  "rand_ot1" => $random[1],
  "rand_ot2" => $random[2],
  "rand_ot3" => $random[3],
  "rand_ot4" => $random[4],
  "c" => $uid_key["c"],
  "bdd_node" => $uid_key,
  "retuid" => $retuid,
);