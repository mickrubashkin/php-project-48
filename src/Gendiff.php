<?php

namespace Differ\Differ;

use function Differ\Parser\parse;
use function Differ\CalculateDiff\calcDiff;
use function Differ\Stringify\stringify;

function genDiff($path1, $path2)
{
    $realPath1 = realpath($path1);
    $realPath2 = realpath($path2);

    if (!$realPath1 || !$realPath2) {
        echo 'File does not exists. Please check the pathes.' . PHP_EOL;
    }

    $coll1 = parse($realPath1);
    $coll2 = parse($realPath2);

    $diff = calcDiff($coll1, $coll2);
    $strDiff = stringify($diff);

    return $strDiff;
}
