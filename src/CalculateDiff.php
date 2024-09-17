<?php

namespace Differ\CalculateDiff;

use function Differ\Functions\isAssociativeArray;
use function Functional\sort;

function calcDiff(array $coll1, array $coll2): array
{
    $mergedColl = array_merge($coll1, $coll2);
    $sortedKeys = sort(array_keys($mergedColl), fn ($left, $right) => strcmp($left, $right));
    $sortedValues = array_map(fn($key) => $mergedColl[$key], $sortedKeys);

    $diff = array_map(function ($k, $v) use ($coll1, $coll2) {
        if (!array_key_exists($k, $coll1)) {
            return ['key' => $k, 'type' => 'added', 'value' => $v];
        } elseif (!array_key_exists($k, $coll2)) {
            return ['key' => $k, 'type' => 'removed', 'value' => $v];
        } elseif (isAssociativeArray($v)) {
            $children = calcDiff($coll1[$k], $coll2[$k]);
            return ['key' => $k, 'type' => 'nested', 'children' => $children];
        } elseif ($coll1[$k] === $coll2[$k]) {
            return ['key' => $k, 'type' => 'unchanged', 'value' => $v];
        } else {
            return ['key' => $k, 'type' => 'updated', 'value' => ['from' => $coll1[$k], 'to' => $coll2[$k]]];
        }
    }, $sortedKeys, $sortedValues);

    return $diff;
}
