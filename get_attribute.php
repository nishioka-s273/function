<?php
function get_attribute($server_name) {
    // IPアドレスで書かないとエラーになる
    $url = "http://10.229.71.229/api/attribute.php?"
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
        return "CURL ERROR : ".$errorNo;
    }

    if ($info['http_code'] !== 200) {
        return "HTTP ERROR : ".$info['http_code'];
    }

    $jsonArray = json_decode($json, true);
    if(count($jsonArray) === 0) {
        echo "CAUGHT ARRAY IS NULL";
    }

    $ret = [];
    $ret['result'] = $jsonArray['result'];
    $ret['attributes'] = $jsonArray['attributes'];
    $ret['key'] = $jsonArray['key'];

    return $ret;
}
?>
<!DOCTYPE html>
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
        <h2>WebAPI</h2>
        <h3>リクエスト</h3>
        <?php 
        $ats = get_attribute("idp1.local");
        echo "result : ".$ats['result']."<br>attributes : ";
        foreach ($ats['attributes'] as $atr) {
            echo $atr." ";
        }
        echo "<br>key : ".$ats['key'];
        ?>
        <!--
        <pre>
        <!--<?php
            //$returnOrigin = 'idp1.local';
            //$imp_returnOrigin = htmlspecialchars($returnOrigin);
        ?>
        --></pre>
        <!--- api.jsのsend_dataに反映させる-->
        <!--<input type="hidden" name="returnOrigin" id="id_returnOrigin" value="<?//=$imp_returnOrigin?>">
        <button data-btn-type="ajax">Data get!</button>
        <br><br>
        <h3>結果</h3>
        <div data-result="">未取得</div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script type="text/javascript" src="api.js"></script>-->
    </body>
</html>