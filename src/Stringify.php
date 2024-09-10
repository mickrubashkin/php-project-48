<?php

namespace Differ\Stringify;

function stringifyValue($v)
{
  if (gettype($v) === 'string' || gettype($v) === 'integer' || gettype($v) === 'double') {
    return $v;
  }

  return json_encode($v);
}

function stringify($coll)
{
  $parts = [];
  $separator = '  ';

  foreach ($coll as $k => $v) {
    if ($v['type'] === 'added') {
      $value = stringifyValue($v['value']);
      $parts[] = "{$separator}+ {$k}: {$value}";
    } elseif ($v['type'] === 'deleted') {
      $value = stringifyValue($v['value']);
      $parts[] = "{$separator}- {$k}: {$value}";
    } elseif ($v['type'] === 'modified') {
      $oldValue = stringifyValue($v['value']['old']);
      $newValue = stringifyValue($v['value']['new']);
      $parts[] = "{$separator}- {$k}: {$oldValue}";
      $parts[] = "{$separator}+ {$k}: {$newValue}";
    } else {
      $value = stringifyValue($v['value']);
      $parts[] = "{$separator}  $k: {$value}";
    }
  }

  $str = '{' . PHP_EOL . implode("\n", $parts) . PHP_EOL . '}' . PHP_EOL;

  return $str;
}