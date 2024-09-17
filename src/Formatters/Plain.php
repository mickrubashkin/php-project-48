<?php

namespace Differ\Formatters\Plain;

use function Differ\Functions\isAssociativeArray;

function stringifyValue(mixed $v)
{
    if (gettype($v) === 'integer' || gettype($v) === 'double') {
        return $v;
    } elseif (gettype($v) === 'string') {
        return "'{$v}'";
    } elseif (isAssociativeArray($v)) {
        return '[complex value]';
    }

    return json_encode($v);
}

function iter(mixed $data, array $path): mixed
{
    $lines = isAssociativeArray($data)
        ? array_map(fn($k, $v) => [$k => $v], array_keys($data), array_values($data))
        : $data;

        $filteredLines = array_filter($lines, fn ($item) => $item['type'] !== 'unchanged');

        $formattedLines = array_map(function ($item) use ($path) {
            if ($item['type'] === 'nested') {
                return iter($item['children'], [...$path, $item['key']]);
            }

            $fullPath = "'" . implode('.', [...$path, $item['key']]) . "'";

            if ($item['type'] === 'updated') {
                $value = $item['value'];
                $from = stringifyValue($value['from']);
                $to = stringifyValue($value['to']);
                return "Property $fullPath was updated. From $from to $to";
            } elseif ($item['type'] === 'added') {
                $value = stringifyValue($item['value']);
                return "Property $fullPath was added with value: $value";
            } elseif ($item['type'] === 'removed') {
                return "Property $fullPath was removed";
            }
        }, $filteredLines);

        return implode("\n", $formattedLines);
}

function formatPlain(array $diff): string
{
    return iter($diff, []);
}
