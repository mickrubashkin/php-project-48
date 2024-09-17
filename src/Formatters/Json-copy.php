<?php

namespace Differ\Formatters\Json;

use function Differ\Functions\isAssociativeArray;

function stringifyValue($v)
{
    $type = gettype($v);

    if ($type === 'string') {
        return '"' . $v . '"';
    } elseif ($type === 'integer' || $type === 'double') {
        return $v;
    } elseif ($type === 'boolean' || $type === 'NULL') {
        return json_encode($v);
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

    $separator = '    ';
    $indent = str_repeat($separator, $depth);
    $bracketIndent = str_repeat($separator, $depth - 1);

    $formatted = array_map(function ($item) use ($depth, $indent) {
        if (!array_key_exists('type', $item)) {
            $key = key($item);
            $v = $item[$key];
            $value = iter($v, $depth + 1);
            return "{$indent}{$key}: $value";
        }
        if ($item['type'] === 'added') {
            $key = $item['key'];
            $value = iter($item['value'], $depth + 1);
            return "{$indent}{$key}: {$value}";
        } if ($item['type'] === 'removed') {
            $key = $item['key'];
            $value = iter($item['value'], $depth + 1);
            return "{$indent}{$key}: {$value}";
        } if ($item['type'] === 'updated') {
            $key = $item['key'];
            $from = iter($item['value']['from'], $depth + 1);
            $to = iter($item['value']['to'], $depth + 1);
            return "{$indent}{$key}: {$from}\n{$indent}{$key}: {$to}";
        } if ($item['type'] === 'unchanged') {
            $key = $item['key'];
            $value = iter($item['value'], $depth + 1);
            return "{$indent}{$key}: {$value}";
        } if (array_key_exists('children', $item)) {
            $key = $item['key'];
            $inner = iter($item['children'], $depth + 1);
            return "{$indent}{$key}: {$inner}";
        }
    }, $lines);

    return "{\n" . implode("\n", $formatted) . "\n{$bracketIndent}}";
}

function formatJson(array $diff): string
{
    // var_dump($diff);
    return iter($diff, 1);
}
