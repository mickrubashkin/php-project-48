<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string|false $path)
{
    if ($path === false) {
        echo 'File does not exists. Please check the pathes.' . PHP_EOL;
    }

    $content = file_get_contents($path);
    $fileType = pathinfo($path, PATHINFO_EXTENSION);

    if ($content === false) {
        echo 'Failed to read file.' . PHP_EOL;
    }
    if (gettype($content) === 'string') {
        if ($fileType === 'json') {
                return json_decode($content, true);
        }

        if ($fileType === 'yaml' || $fileType === 'yml') {
            return Yaml::parse($content);
        }
    }
}
