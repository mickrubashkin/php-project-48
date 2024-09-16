<?php

namespace Differ\Stringify;

use function Differ\CalculateDiff\isAssociativeArray;

function stringifyValue($v)
{
    if (gettype($v) === 'string' || gettype($v) === 'integer' || gettype($v) === 'double') {
        return $v;
    }

    return json_encode($v);
}

function mkPad(string $separator, $depth, string $pad, string $type = 'nested'): string
{
    if ($type === 'nested') {
        return substr(str_repeat($separator, $depth), 0, -2) . $pad;
    }

    if ($type === 'plain') {
        return str_repeat($separator, $depth);
    }
}

function stringify($data, int $depth = 1): string
{
    $parts = [];
    $separator = '****';
    $symbol = [
        'added' => '+ ',
        'deleted' => '- ',
        'nested' => '  ',
        'unchanged' => '  ',
    ];

    foreach ($data as $k => $v) {
        if (!isAssociativeArray($v)) {
            $pad = mkPad($separator, $depth + 2, '  ', 'plain');
            $parts[] = "{$pad}{$k}: {$v}";
        } elseif (!array_key_exists('type', $v)) {
            $pad = mkPad($separator, $depth, '  ');
            $value = stringify($v, $depth + 1);
            $parts[] = "{$pad}$value";
        } elseif ($v['isAssocValue']) {
            if ($v['type'] === 'added') {
                $value = stringify($v['value']);
                $pad = mkPad($separator, $depth, $symbol['added']);
                $bracketPad = mkPad($separator, $depth, ' ', 'plain');
                $parts[] = "{$pad}{$k}: {\n{$value}\n{$bracketPad}}";
            } elseif ($v['type'] === 'deleted') {
                $value = stringify($v['value']);
                $pad = mkPad($separator, $depth, $symbol['deleted']);
                $bracketPad = mkPad($separator, $depth, ' ', 'plain');
                $parts[] = "{$pad}{$k}: {\n{$value}\n{$bracketPad}}";
            } elseif ($v['type'] === 'modified') {
                $oldValue = stringify($v['value']['old']);
                $newValue = stringify($v['value']['new']);
                $pad1 = mkPad($separator, $depth, $symbol['deleted']);
                $pad2 = mkPad($separator, $depth, $symbol['added']);
                $bracketPad = mkPad($separator, $depth, ' ', 'plain');
                $parts[] = "{$pad1}{$k}: {\n{$oldValue}\n{$bracketPad}";
                $parts[] = "{$pad2}{$k}: {\n{$newValue}\n{$bracketPad}}";
            } elseif ($v['type'] === 'nested') {
                $newDepth = $depth + 1;
                $value = stringify($v['value'], $newDepth);
                $pad = mkPad($separator, $depth, $symbol['nested']);
                $bracketPad = mkPad($separator, $depth, ' ', 'plain');
                $parts[] = "{$pad}{$k}: {\n{$value}\n{$bracketPad}}";
            } else {
                $value = stringify($v['value']);
                $pad = mkPad($separator, $depth, $symbol['unchanged']);
                $parts[] = "{$pad}$k: {\n{$value}\n}";
            }
        } else {
            if ($v['type'] === 'added') {
                $value = stringifyValue($v['value']);
                $pad = mkPad($separator, $depth, $symbol['added']);
                $parts[] = "{$pad}{$k}: {$value}";
            } elseif ($v['type'] === 'deleted') {
                $value = stringifyValue($v['value']);
                $pad = mkPad($separator, $depth, $symbol['deleted']);
                $parts[] = "{$pad}{$k}: {$value}";
            } elseif ($v['type'] === 'modified') {
                $oldValue = stringifyValue($v['value']['old']);
                $newValue = stringifyValue($v['value']['new']);
                $pad1 = mkPad($separator, $depth, $symbol['deleted']);
                $pad2 = mkPad($separator, $depth, $symbol['added']);
                $parts[] = "{$pad1}{$k}: {$oldValue}";
                $parts[] = "{$pad2}{$k}: {$newValue}";
            } elseif ($v['type'] === 'nested') {
                $newDepth = $depth + 1;
                $value = stringify($v['value'], $newDepth);
                $pad = mkPad($separator, $depth, $symbol['nested']);
                $parts[] = "{$pad}{$k}: \n{$value}";
            } else {
                $value = stringifyValue($v['value']);
                $pad = mkPad($separator, $depth, $symbol['unchanged']);
                $parts[] = "{$pad}$k: {$value}";
            }
        }
    }




    $str = implode("\n", $parts);

    return $str;
}
