<?php

namespace Differ\Functions;

function mkPad(string $separator, $depth, string $pad, string $type = 'nested'): string
{
    if ($type === 'nested') {
        return substr(str_repeat($separator, $depth), 0, -2) . $pad;
    }

    if ($type === 'plain') {
        return str_repeat($separator, $depth);
    }
}

function isAssociativeArray($array)
{
    if (!is_array($array)) {
        return false;
    }

    $keys = array_keys($array);
    if ($keys !== range(0, count($array) - 1)) {
        return true;
    }

    return false;
}
