<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\CalculateDiff\calcDiff;
use function Differ\Formatters\getFormatter;

function genDiff(string $path1, string $path2, string $format = 'stylish'): string
{
    $realPath1 = realpath($path1);
    $realPath2 = realpath($path2);

    if (!$realPath1 || !$realPath2) {
        echo 'File does not exists. Please check the pathes.' . PHP_EOL;
    }

    $coll1 = parse($realPath1);
    $coll2 = parse($realPath2);

    $diff = calcDiff($coll1, $coll2);

    $formatter = getFormatter($format);
    $strDiff = $formatter($diff);

    return $strDiff;
}
