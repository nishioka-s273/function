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
        <pre><?php
            $attr = array('att1', 'att2', 'att3');
            $attr_val = array('val1', 'val2', 'val3');
            $imp_attr = htmlspecialchars(implode(',', $attr));
            $imp_val = htmlspecialchars(implode(',', $attr_val));
        ?></pre>
        <input type="hidden" name="attr" id="id_attr" value="<?=$imp_attr?>"><!--- api.jsのsend_dataに反映させる-->
        <input type="hidden" name="attr_val" id="id_attr_val" value="<?=$imp_val?>">
        <!--ul>
            <li>a:Admin</li>
            <li>o:Operatot</li>
            <li>g:Guest</li>
            <li>e::Empty</li>
        </ul-->
        <button data-btn-type="ajax">Data get!</button>
        <br><br>
        <h3>結果</h3>
        <div data-result="">未取得</div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script type="text/javascript" src="api.js"></script>
    </body>
</html>