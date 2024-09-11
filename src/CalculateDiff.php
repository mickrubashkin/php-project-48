<?php

namespace Differ\CalculateDiff;

function calcDiff($coll1, $coll2)
{
    uksort($coll1, fn($a, $b) => $a <=> $b);
    uksort($coll2, fn($a, $b) => $a <=> $b);

    $mergedColl = array_merge($coll1, $coll2);
    $diff = [];

    foreach ($mergedColl as $k => $v) {
        if (!array_key_exists($k, $coll1)) {
            $diff[$k] = [
            'type'  => 'added',
            'value' => $v
            ];
        } elseif (!array_key_exists($k, $coll2)) {
            $diff[$k] = [
            'type' => 'deleted',
            'value' => $v
            ];
        } elseif ($coll1[$k] !== $coll2[$k]) {
            $diff[$k] = [
            'type' => 'modified',
            'value' => [
            'old' => $coll1[$k],
            'new' => $coll2[$k]
            ]
            ];
        } else {
            $diff[$k] = [
            'type' => 'unchanged',
            'value' => $v
            ];
        }
    }

    return $diff;
}
