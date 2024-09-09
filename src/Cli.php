<?php

require_once "Parser.php";
use function Gendiff\Parser\parse;

$doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help       Show this screen
  -v --version    Show version
  --format <fmt>  Report format [default: stylish]

DOC;

$args = Docopt::handle($doc, array('version'=>'GenDiff 1.0'));

foreach ($args as $k => $v) {
  echo $k.': '.json_encode($v).PHP_EOL;
}

$path1 = $args['<firstFile>'];
$path2 = $args['<secondFile>'];

$file1 = parse($path1);
$file2 = parse($path2);

echo "======debug=======".PHP_EOL;
var_dump($file1);
var_dump($file2);
echo '======debug======='.PHP_EOL;