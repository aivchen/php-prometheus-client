<?php

declare(strict_types=1);

namespace Zlodes\PrometheusExporter\Tests\Collector\ByType;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use PHPUnit\Framework\TestCase;
use Zlodes\PrometheusExporter\Collector\ByType\GaugeCollector;
use Zlodes\PrometheusExporter\DTO\MetricValue;
use Zlodes\PrometheusExporter\Exceptions\StorageWriteException;
use Zlodes\PrometheusExporter\MetricTypes\Gauge;
use Zlodes\PrometheusExporter\Storage\Storage;

class GaugeCollectorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testSetValue(): void
    {
        $gauge = new Gauge('body_temperature', 'Body temperature in Celsius');

        $collector = new GaugeCollector(
            $gauge,
            $storageMock = Mockery::mock(Storage::class),
            new NullLogger()
        );

        /** @var MetricValue $metricValue */
        $storageMock
            ->expects('setValue')
            ->with(Mockery::capture($metricValue));

        $collector
            ->withLabels([
                'source' => 'armpit',
            ])
            ->setValue(36.6);

        $expectedLabels = [
            'source' => 'armpit',
        ];

        self::assertEquals(36.6, $metricValue->value);
        self::assertEquals('body_temperature', $metricValue->metricNameWithLabels->metricName);
        self::assertEquals($expectedLabels, $metricValue->metricNameWithLabels->labels);
    }

    #[DataProvider('incrementDataProvider')]
    public function testIncrement(int|float $value): void
    {
        $gauge = new Gauge('score', 'Quiz game players score');

        $collector = new GaugeCollector(
            $gauge,
            $storageMock = Mockery::mock(Storage::class),
            new NullLogger()
        );

        /** @var MetricValue $metricValue */
        $storageMock
            ->expects('incrementValue')
            ->with(Mockery::capture($metricValue));

        $collector
            ->withLabels([
                'player' => 'user1',
            ])
            ->increment($value);

        $expectedLabels = [
            'player' => 'user1',
        ];

        self::assertEquals($value, $metricValue->value);
        self::assertEquals('score', $metricValue->metricNameWithLabels->metricName);
        self::assertEquals($expectedLabels, $metricValue->metricNameWithLabels->labels);
    }

    public function testStorageErrorWhileIncrementingValue(): void
    {
        $gauge = new Gauge('score', 'Quiz game players score');

        $collector = new GaugeCollector(
            $gauge,
            $storageMock = Mockery::mock(Storage::class),
            $loggerMock = Mockery::mock(LoggerInterface::class)
        );

        $storageMock
            ->expects('incrementValue')
            ->andThrow(new StorageWriteException('Cannot write'));

        $loggerMock
            ->expects('error');

        $collector->increment();
    }

    public function testStorageErrorWhileSettingValue(): void
    {
        $gauge = new Gauge('score', 'Quiz game players score');

        $collector = new GaugeCollector(
            $gauge,
            $storageMock = Mockery::mock(Storage::class),
            $loggerMock = Mockery::mock(LoggerInterface::class)
        );

        $storageMock
            ->expects('setValue')
            ->andThrow(new StorageWriteException('Cannot write'));

        $loggerMock
            ->expects('error');

        $collector->setValue(42);
    }

    public static function incrementDataProvider(): iterable
    {
        yield 'positive integer' => [42];
        yield 'negative integer' => [-10];
        yield 'positive float' => [10.5];
        yield 'negative float' => [-1.25];
    }
}