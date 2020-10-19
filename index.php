<html lang="ja">
    <head>
    <meta charset="UTF-8">
        <title>WebAPI test</title>
        <style type="text/css">
        body,
        input,
        button {
            font-size:: 30px;
        }
        </style>
    </head>
    <body>
        <h2>検証用ページ</h2>
    </body>
</html>
<?php
require "get_attribute.php";
require "get_zi.php";

$ats = get_attribute("idp1.local");
echo "result : ".$ats['result']."<br>attributes : ";
foreach ($ats['attributes'] as $atr) {
    echo $atr." ";
}
echo "<br>key : {a : ".$ats['key']['a']
             .", b : ".$ats['key']['b']
             .", p : ".$ats['key']['p']
             .", r : ".$ats['key']['r']
             .", Y : [".$ats['key']['Y'][0]
             .", ".$ats['key']['Y'][1]."]}<br>";
//echo "<br>key : ".$ats['key'];

$i = 0;
$rand = [];
foreach($ats['attributes'] as $atr) {
    $rand[$i] = rand(1,10);
    $i = $i + 1;
}
echo "random int : ";
print_r($rand);
$arr = get_zi($ats['attributes'], $ats['key'], $rand, "idp1.local");
print_r($arr);