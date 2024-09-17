<?php

namespace Differ\Fromatters\Stylish;

use function Differ\Functions\isAssociativeArray;

function stringifyValue($v)
{
    if (gettype($v) === 'string' || gettype($v) === 'integer' || gettype($v) === 'double') {
        return $v;
    }

    return json_encode($v);
}

function iter(mixed $data, $depth = 1): mixed
{
    if (gettype($data) !== 'array') {
        return stringifyValue($data);
    }

        $lines = [];

    if (isAssociativeArray($data)) {
        foreach ($data as $k => $v) {
            $lines[] = [$k => $v];
        }
    } else {
        $lines = $data;
    }

        $indentSize = $depth * 4;
        $currentIndent = str_repeat(' ', $indentSize);
        $addedIndent = substr($currentIndent, 0, -2) . '+ ';
        $removedIndent = substr($currentIndent, 0, -2) . '- ';
        $bracketIndent = str_repeat(' ', $indentSize - 4);

        $formatted = array_map(function ($item) use ($depth, $addedIndent, $removedIndent, $currentIndent) {
            if (!array_key_exists('type', $item)) {
                $k = key($item);
                $v = $item[$k];
                $value = iter($v, $depth + 1);
                return "{$currentIndent}{$k}: $value";
            }
            if ($item['type'] === 'added') {
                $key = $item['key'];
                $value = iter($item['value'], $depth + 1);
                return "{$addedIndent}{$key}: {$value}";
            } if ($item['type'] === 'removed') {
                $key = $item['key'];
                $value = iter($item['value'], $depth + 1);
                return "{$removedIndent}{$key}: {$value}";
            } if ($item['type'] === 'updated') {
                $key = $item['key'];
                $from = iter($item['value']['from'], $depth + 1);
                $to = iter($item['value']['to'], $depth + 1);
                return "{$removedIndent}{$key}: {$from}\n{$addedIndent}{$key}: {$to}";
            } if ($item['type'] === 'unchanged') {
                $key = $item['key'];
                $value = iter($item['value'], $depth + 1);
                return "{$currentIndent}{$key}: {$value}";
            } if (array_key_exists('children', $item)) {
                $key = $item['key'];
                $inner = iter($item['children'], $depth + 1);
                return "{$currentIndent}{$key}: {$inner}";
            }
        }, $lines);

        return "{\n" . implode("\n", $formatted) . "\n{$bracketIndent}}";
}

function formatStylish(array $diff): string
{
    return iter($diff, 1);
}
