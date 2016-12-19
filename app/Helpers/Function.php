<?php

function pr()
{
    $trace = debug_backtrace()[0];
    echo '<pre xstyle="font-size:9px;font: small monospace;">';
    echo PHP_EOL.str_repeat('=', 100).PHP_EOL;
    echo 'file '.$trace['file'].' line '.$trace['line'];
    echo PHP_EOL.str_repeat('-', 100).PHP_EOL;

    if (1 === func_num_args()) {
        $args = func_get_arg(0);
    } else {
        $args = func_get_args();
    }

    echo prx($args);
    echo PHP_EOL.str_repeat('=', 100).PHP_EOL;
    echo '</pre>';
}

function prx($s)
{
    $a = [
        'Object'.PHP_EOL.' \*RECURSION\*'     => '#RECURSION',
        '    '                                => '  ',
        PHP_EOL.PHP_EOL                       => PHP_EOL,
        ' \('                                 => '(',
        ' \)'                                 => ')',
        '\('.PHP_EOL.'\s+\)'                  => '()',
        'Array\s+\(\)'                        => 'Array()',
        //' (=> Array|Object)'.PHP_EOL.'\s+\(' => ' $1(',
    ];

    $s = print_r($s, true);
    foreach ($a as $key => $val) {
        $s = preg_replace('#'.$key.'#', $val, $s);
    }

    return $s;
}

function html_encode($in)
{
    $t = '<table border=1 cellspacing="0" cellpadding="0">';
    foreach ($in as $key => $value) {
        if (true === is_array($value)) {
            $t .= '<tr><td>'.$key.'</td><td>'.html_encode($value).'</td></tr>';
        } else {
            $t .= '<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
        }
    }

    return $t.'</table>';
}

function object2array($object)
{
    return json_decode(json_encode($object), true);
}

function decode_file($filename)
{
    if (false === file_exists($filename)) {
        throw new Exception($filename.' file not exists');
    }
    $contents = file_get_contents($filename);
    $ext      = pathinfo($filename, PATHINFO_EXTENSION);
    switch ($ext) {
        case 'yaml':
        case 'yml':
            $result = yaml_parse($contents, true);
            break;
        case 'json':
            $result = json_decode($contents, true);
            if (json_last_error()) {
                throw new \Exception($filename.' Invalid JSON syntax');
            }
            break;
        default:
            throw new \Exception($ext.' not support');
            break;
    }

    return $result;
}

function is_assoc($array)
{
    $keys = array_keys($array);

    return $keys !== array_keys($keys);
}
