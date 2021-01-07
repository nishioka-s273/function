<?php

function setup() {
    $ret = [];
    $ret['x'] = 3;
    $ret['G'] = [2,2];
    $ret['a'] = 0;
    $ret['b'] = 1;
    $ret['p'] = 5;
    $ret['r'] = 3;

    $command = "python -c 'import crypt; print crypt.pub_key(".$ret['x'].", [".$ret['G'][0].", ".$ret['G'][1]."], ".$ret['a'].", ".$ret['b'].", ".$ret['p'].")'";

    exec($command, $output);
    $ret['Y'] = substr($output[0],1);
    $ret['Y'] = substr($ret['Y'], 0, -1);
    $ret['Y'] = explode(',', $ret['Y']);
    $ret['Y'][1] = substr($ret['Y'][1], 1);

    return $ret;
    exit(0);
}

//$setup = setup();
//print_r($setup);