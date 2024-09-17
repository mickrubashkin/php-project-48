<?php

namespace Differ\Formatters;

use function Differ\Fromatters\Stylish\formatStylish;
use function Differ\Formatters\Plain\formatPlain;
use function Differ\Formatters\Json\formatJson;

function getFormatter(string $formatName)
{
    if ($formatName === 'stylish') {
        return fn($diff) => formatStylish($diff);
    }
    if ($formatName === 'plain') {
        return fn($diff) => formatPlain($diff);
    }
    if ($formatName === 'json') {
        return fn($diff) => formatJson($diff);
    }
}
