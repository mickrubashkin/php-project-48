<?php

namespace Differ\Formatters\Json;

function convertToAssoc($arr)
{
    $coll = [];
    foreach ($arr as $item) {
        $key = $item['key'];
        $type = $item['type'];

        if ($type === 'nested') {
            $children = $item['children'];
            $newChildren = convertToAssoc($children);
            $coll[$key] = ['type' => $type, 'children' => $newChildren];
        } elseif ($type === 'updated') {
            $from = $item['value']['from'];
            $to = $item['value']['to'];
            $coll[$key] = ['type' => $type, 'from' => $from, 'to' => $to];
        } else {
            $value = $item['value'];
            $coll[$key] = ['type' => $type, 'value' => $value];
        }
    }

    return $coll;
}

function formatJson(array $diff): string
{
    $coll = convertToAssoc($diff);
    return json_encode($coll, JSON_PRETTY_PRINT);
}
