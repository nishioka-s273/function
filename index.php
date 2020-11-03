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
<?php
require "get_attribute.php";
require "get_zi.php";
require "get_ci.php";

function return_word($data) {
    if ($data == 1) {
        $ats = get_attribute("idp1.local");
        echo "<h3>get_attribute</h3>";
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
        echo "session_id : ".$ats['session_id']."<br>";

        //setcookie('session_id', $ats['session_id'], time()+60*60);
        $session_id = $ats['session_id'];

        echo "<h3>get_zi</h3>";
        $i = 0;
        $rand = [];
        foreach($ats['attributes'] as $atr) {
            $rand[$i] = rand(1,10);
            $i = $i + 1;
        }
        echo "random int : ";

        print_r($rand);
        echo "<br>";
        $wijs = get_zi($ats['attributes'], $ats['key'], $rand, "idp1.local", $session_id, "idp1_user1");
        echo "result : ".$wijs['result']."<br>";
        echo "hash_function : ".$wijs['hash_function']."<br>";
        echo "algo : ".$wijs['algo']."<br>";
        echo "key : ".$wijs['key']."<br>w_ij : ";
        print_r($wijs['w_ij']);
        echo "<br>attribute value : ";
        print_r($wijs['A_i']);

        echo "<h3>get_ci</h3>";
        $ci = get_ci($wijs['hash_function'], $wijs['algo'], $wijs['key'], $wijs['w_ij'], $wijs['A_i'],$rand);

        print_r($ci);
        return $ci[0];
    }
    else {
        return "error";
    }
}

return_word(1);
?>
</body>
</html>