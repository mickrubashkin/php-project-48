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
    uksort($coll1, fn($a, $b) => $a <=> $b);
    uksort($coll2, fn($a, $b) => $a <=> $b);

    $mergedColl = array_merge($coll1, $coll2);
    $diff = [];

    foreach ($mergedColl as $k => $v) {
        $isAssoc = isAssociativeArray($v);

        if (!array_key_exists($k, $coll1)) {
            $diff[$k] = [
            'type'  => 'added',
            'value' => $v,
            'isAssocValue' => $isAssoc
            ];
        } elseif (!array_key_exists($k, $coll2)) {
            $diff[$k] = [
            'type' => 'deleted',
            'value' => $v,
            'isAssocValue' => $isAssoc
            ];
        } elseif ($coll1[$k] === $coll2[$k]) {
            $diff[$k] = [
            'type' => 'unchanged',
            'value' => $v,
            'isAssocValue' => $isAssoc
            ];
        } elseif (isAssociativeArray($v)) {
            $inner = calcDiff($coll1[$k], $coll2[$k]);
            $diff[$k] = [
                'type' => 'nested',
                'value' => $inner,
                'isAssocValue' => true
            ];
        } else {
            $diff[$k] = [
            'type' => 'modified',
            // 'oldValue' => [
            //     'isAssocValue' => isAssociativeArray($coll1[$k]),
            //     'value' => $coll1[$k]
            // ],
            // 'newValue' => [
            //     'isAssocValue' => isAssociativeArray($coll2[$k]),
            //     'value' => $coll2[$k]
            // ]
            'value' => [
                'old' => $coll1[$k],
                'new' => $coll2[$k]
            ],
            // 'isAssocValue' => $isAssoc
            ];
        }
    }

    uksort($diff, fn ($k1, $k2) => $k1 <=> $k2);

    return $diff;
}
