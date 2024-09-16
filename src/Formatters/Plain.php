<?php

namespace Differ\Formatters\Plain;

use function Differ\Functions\isAssociativeArray;

function stringifyValue($v)
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

function iter(mixed $data, $path): mixed
{
    $lines = [];

    if (isAssociativeArray($data)) {
        foreach ($data as $k => $v) {
            $lines[] = [$k => $v];
        }
    } else {
        $lines = $data;
    }

        $filteredLines = array_filter($lines, fn ($item) => $item['type'] !== 'unchanged');

        $formattedLines = array_map(function ($item) use ($path) {
            if ($item['type'] === 'nested') {
                return iter($item['children'], [...$path, $item['key']]);
            }

            $fullPath = "'" . implode('.', [...$path, $item['key']]) . "'";
            $value = $item['value'];
            $from = '';
            $to = '';
            $result = '';

            if ($item['type'] === 'updated') {
                $from = stringifyValue($value['from']);
                $to = stringifyValue($value['to']);
                $result = "Property $fullPath was updated. From $from to $to";
            } elseif ($item['type'] === 'added') {
                $value = stringifyValue($value);
                $result = "Property $fullPath was added with value: $value";
            } elseif ($item['type'] === 'removed') {
                $result = "Property $fullPath was removed";
            }

            return $result;
        }, $filteredLines);

        return implode("\n", $formattedLines);
}

function formatPlain(array $diff): string
{
    return iter($diff, []);
}
