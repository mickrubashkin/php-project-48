<?php

namespace Differ\Parser;

function parse($path)
{
  $content = file_get_contents(getcwd() . '/' . $path);
  return json_decode($content, true);
}