<?php
require "/var/www/function/get_attribute.php";
require "/var/www/function/get_zi.php";

$sp_attr = get_attribute("idp1.local");
$result = $sp_attr["result"];
if ($result !== "OK") {
    throw new Exception("Unable to get the attributes.");
}

$attr = $sp_attr["attributes"];
$key = $sp_attr["key"];
$session_id = $sp_attr["session_id"];

if (empty($attributes["uid"])) {
  throw new Exception("Missing uid attribute.");
}
$uid = $attributes["uid"][0];
// デバッグ用
//$uid = "idp1_user1";
$i = 0;
$rand = [];
foreach($attr as $atr) {
    $rand[$i] = rand(1,100);
    $i = $i + 1;
}

$arr = get_zi($attr, $key, $rand, "idp1.local", $session_id, $uid);
$mail = $uid."@example.net".$result;
$attributes["mail"] = array($mail);