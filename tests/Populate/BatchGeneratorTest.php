<?php

namespace Biblioteca\TypesenseBundle\Tests\Populate;

use Biblioteca\TypesenseBundle\Populate\BatchGenerator;
use PHPUnit\Framework\TestCase;

class BatchGeneratorTest extends TestCase
{
    /**
     * @param int[]   $source
     * @param int[][] $result
     *
     * @dataProvider batchDataProvider
     */
    public function testGenerate(array $source, int $batchSize, array $result): void
    {
        /** @var BatchGenerator<int> $batchGenerator */
        $batchGenerator = new BatchGenerator($source, $batchSize);
        $this->assertSame($result, iterator_to_array($batchGenerator->generate()));
    }

    /**
     * @return array<string, array{source: int[], batchSize: int, result: int[][]}>
     */
    public static function batchDataProvider(): array
    {
        return [
            'empty source' => [
                'source' => [],
                'batchSize' => 2,
                'result' => [],
            ],
            'byTwo even' => [
                'source' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                'batchSize' => 2,
                'result' => [[1, 2], [3, 4], [5, 6], [7, 8], [9, 10]],
            ],
            'byTwo odd' => [
                'source' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                'batchSize' => 2,
                'result' => [[1, 2], [3, 4], [5, 6], [7, 8], [9, 10], [11]],
            ],
            'byTen with one item' => [
                'source' => [1],
                'batchSize' => 10,
                'result' => [[1]],
            ],
        ];
    }

    public function testInvalidSize(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new BatchGenerator([1, 2, 3], -1);
    }
}
