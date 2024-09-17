<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $path)
{
    $content = file_get_contents($path);
    $fileType = pathinfo($path)['extension'];

    if ($fileType === 'json') {
        return json_decode($content, true);
    }

    if ($fileType === 'yaml' || $fileType === 'yml') {
        return Yaml::parse($content);
    }
}
