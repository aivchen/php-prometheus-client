<?php

declare(strict_types=1);

namespace Zlodes\PrometheusClient\Tests\Storage;

use PHPUnit\Framework\Attributes\DataProvider;
use Zlodes\PrometheusClient\Storage\InMemorySummary;
use PHPUnit\Framework\TestCase;

class InMemorySummaryTest extends TestCase
{
    #[DataProvider('sumAndCountDataProvider')]
    public function testSummarySumAndCount(array $items, int $expectedCount, float $expectedSum): void
    {
        $summary = new InMemorySummary($items);

        self::assertEquals($expectedCount, $summary->getCount());
        self::assertEquals($expectedSum, $summary->getSum());
    }

    #[DataProvider('quantilesDataProvider')]
    public function testGetQuantile(array $items, float $actualResult, float $expectedResult): void
    {
        $summary = new InMemorySummary($items);

        $actualResult = $summary->getQuantile($actualResult);

        self::assertEquals($expectedResult, $actualResult);
    }

    public static function sumAndCountDataProvider(): iterable
    {
        // [
        //   items array,
        //   expected count,
        //   expected sum
        // ]

        yield 'empty' => [
            [],
            0,
            0,
        ];

        yield 'one value' => [
            [42],
            1,
            42,
        ];

        yield 'two values' => [
            [42],
            1,
            42,
        ];
    }

    public static function quantilesDataProvider(): iterable
    {
        // [
        //   items array,
        //   quantile,
        //   expected value
        // ]

        yield 'empty' => [
            [],
            0.5,
            0
        ];

        yield 'one value 0.01' => [
            [42],
            0.01,
            42
        ];

        yield 'one value 0.5' => [
            [42],
            0.5,
            42
        ];

        yield 'one value 0.99' => [
            [42],
            0.99,
            42
        ];

        yield 'one value 1.00' => [
            [42],
            1,
            42
        ];

        yield 'two values 0.01' => [
            [100, 300],
            0.01,
            100
        ];

        yield 'two values 0.5' => [
            [100, 300],
            0.5,
            100
        ];

        yield 'two values 0.99' => [
            [100, 300],
            0.99,
            100
        ];

        yield 'two values 1.00' => [
            [100, 300],
            1,
            300
        ];

        yield 'many values 0.01' => [
            [100, 200, 300, 400, 500, 600, 700, 800, 900],
            0.00000001,
            100
        ];

        yield 'many values 0.5' => [
            [100, 200, 300, 400, 500, 600, 700, 800, 900],
            0.5,
            500
        ];

        yield 'many values 0.99' => [
            [100, 200, 300, 400, 500, 600, 700, 800, 900],
            0.99,
            900
        ];

        yield 'many values 1.00' => [
            [100, 200, 300, 400, 500, 600, 700, 800, 900],
            1,
            900
        ];

        yield 'many values 0.701' => [
            [100, 200, 300, 400, 500, 600, 700, 800, 900],
            0.701,
            700
        ];
    }
}
