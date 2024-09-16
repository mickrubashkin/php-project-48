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
            // list('type' => $type, 'key' => $k, 'value' => $value) = $item;
            if ($item['type'] === 'added') {
                $key = $item['key'];
                $value = iter($item['value'], $depth + 1);
                return "{$addedIndent}{$key}: {$value}";
            } if ($item['type'] === 'deleted') {
                $key = $item['key'];
                $value = iter($item['value'], $depth + 1);
                return "{$removedIndent}{$key}: {$value}";
            } if ($item['type'] === 'modified') {
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

function stringify(array $diff, string $format = 'stylish'): string
{
    return iter($diff, 1);
}
