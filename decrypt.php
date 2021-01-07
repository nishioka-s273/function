<?php

/*function decrypt($c, $r, $x, $G, $a, $b, $p) {
    $command = "a";
    //$command = "python -c 'import crypt; print crypt.decrypt(".$c.", ".$r.", ".$x.", [".$G[0].", ".$G[1]."], ".$a.", ".$b.", ".$p.")'";

    return($command);
    exit(0);
}*/
function decrypt($c, $r, $x, $G, $a, $b, $p){
    $command = "python -c 'import crypt; print crypt.decrypt(".$c.", ".$r.", ".$x.", [".$G[0].", ".$G[1]."], ".$a.", ".$b.", ".$p.")'";
    exec($command, $output);

    return $output[0];
}
