<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($path)
{
    $content = file_get_contents($path);
    $fileType = pathinfo($path)['extension'];
    $coll = null;

    if ($fileType === 'json') {
        $coll = json_decode($content, true);
    }

    if ($fileType === 'yaml' || $fileType === 'yml') {
        $coll = Yaml::parse($content);
    }

    return $coll;
}
