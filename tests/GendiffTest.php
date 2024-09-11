<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GendiffTest extends TestCase
{
    public function testGendiffPlain(): void
    {
        $expected = file_get_contents(__DIR__ . '/fixtures/plain.txt');
        $result = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json');
        $this->assertEquals($expected, $result);
    }
}
