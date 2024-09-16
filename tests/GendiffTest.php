<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GendiffTest extends TestCase
{
    public function testGenerateDiffForJson(): void
    {
        $expected = file_get_contents(__DIR__ . '/fixtures/stylish.txt');
        $actual = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json');
        $this->assertEquals($expected, $actual);
    }

    public function testGenerateDiffForYaml(): void
    {
        $expected = file_get_contents(__DIR__ . '/fixtures/stylish.txt');
        $actual = genDiff('tests/fixtures/file1.yml', 'tests/fixtures/file2.yml');
        $this->assertEquals($expected, $actual);
    }

    public function testGeneratePlainDiff(): void
    {
        $expected = file_get_contents(__DIR__ . '/fixtures/plain.txt');
        $actual = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json', 'plain');
        $this->assertEquals($expected, $actual);
    }
}
