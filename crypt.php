<?php
function encrypt($r, $mes, $Y, $a, $b, $p) {
    $command = "python -c 'import crypt; print crypt.crypt(".$r.", ".$mes.", [".$Y[0].", ".$Y[1]."], ".$a.", ".$b.", ".$p.")'";

    exec($command, $output);

    return $output[0];
    exit(0);
}
?>