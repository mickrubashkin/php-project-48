<?php

namespace Differ\Parser;

function parse($path)
{
    $content = file_get_contents($path);
    return json_decode($content, true);
}
