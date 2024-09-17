<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\CalculateDiff\calcDiff;
use function Differ\Formatters\getFormatter;

function genDiff(string $path1, string $path2, string $format = 'stylish'): string
{
    $coll1 = parse($path1);
    $coll2 = parse($path2);

    $diff = calcDiff($coll1, $coll2);

    $formatter = getFormatter($format);
    $strDiff = $formatter($diff);

    return $strDiff;
}
