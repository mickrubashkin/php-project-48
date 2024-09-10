<?php

namespace Differ\Differ;

use function Differ\Parser\parse;
use function Differ\CalculateDiff\calcDiff;
use function Differ\Stringify\stringify;

function genDiff($path1, $path2)
{
  $coll1 = parse($path1);
  $coll2 = parse($path2);

  $diff = calcDiff($coll1, $coll2);
  $strDiff = stringify($diff);

  return $strDiff;
}