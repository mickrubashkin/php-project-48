<?php

namespace Differ\Formatters\Json;

function convertToAssoc(array $arr)
{
    $coll = array_reduce($arr, function ($acc, $item) {
        ['key' => $key, 'type' => $type] = $item;

        if ($type === 'nested') {
            $children = $item['children'];
            $newChildren = convertToAssoc($children);
            return [...$acc, $key => ['type' => $type, 'children' => $newChildren]];
        } elseif ($type === 'updated') {
            ['from' => $from, 'to' => $to] = $item['value'];
            return [...$acc, $key => ['type' => $type, 'from' => $from, 'to' => $to]];
        } else {
            $value = $item['value'];
            return [...$acc, $key => ['type' => $type, 'value' => $value]];
        }
    }, []);

    return $coll;
}

function formatJson(array $diff): string
{
    $coll = convertToAssoc($diff);
    return json_encode($coll, JSON_PRETTY_PRINT);
}
