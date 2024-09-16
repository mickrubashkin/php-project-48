<?php

namespace Differ\Formatters;

use function Differ\Fromatters\Stylish\formatStylish;
use function Differ\Formatters\Plain\formatPlain;

function getFormatter(string $formatName)
{
    if ($formatName === 'stylish') {
        return fn($diff) => formatStylish($diff);
    }
    if ($formatName === 'plain') {
        return fn($diff) => formatPlain($diff);
    }
}
