<?php

function get_ci ($hash_function, $algo, $key, $wij, $A, $rand) {
    $ci = [];
    for ($i=0; $i<count($A); $i++) {
        if ($hash_function === 'hash_hmac') {
            $hash = hexdec(hash_hmac($algo, $rand[$i], $key, true));
            if ($wij[$i][$A[$i]-1] === $hash) {
                $ci[$i] = 0;
            }
            else {
                $ci[$i] = 1;
            }
        }
        else {
            die ("No acceptable type of hash_function");
        }
    }

    return $ci;
}
?>