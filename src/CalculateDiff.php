<?php

namespace Differ\CalculateDiff;

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

function calcDiff(array $coll1, array $coll2): array
{
    $mergedColl = array_merge($coll1, $coll2);
    $diff = [];

    foreach ($mergedColl as $k => $v) {
        if (!array_key_exists($k, $coll1)) {
            $diff[] = [
                'key' => $k,
                'type' => 'added',
                'value' => $v,
            ];
        } elseif (!array_key_exists($k, $coll2)) {
            $diff[] = [
                'key' => $k,
                'type' => 'deleted',
                'value' => $v,
            ];
        } elseif (isAssociativeArray($v)) {
            $inner = calcDiff($coll1[$k], $coll2[$k]);
            $diff[] = [
                'key' => $k,
                'type' => 'nested',
                'children' => $inner,
            ];
        } elseif ($coll1[$k] === $coll2[$k]) {
            $diff[] = [
                'key' => $k,
                'type' => 'unchanged',
                'value' => $v,
            ];
        } else {
            $diff[$k] = [
                'key' => $k,
                'type' => 'modified',
                'value' => [
                    'from' => $coll1[$k],
                    'to' => $coll2[$k]
                ]
            ];
        }
    }

    usort($diff, fn ($v1, $v2) => $v1['key'] <=> $v2['key']);

    return $diff;
}
