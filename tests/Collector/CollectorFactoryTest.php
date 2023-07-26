<?php

declare(strict_types=1);

namespace Zlodes\PrometheusClient\Tests\Collector;

use Mockery;
use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Psr\Log\NullLogger;
use Zlodes\PrometheusClient\Collector\CollectorFactory;
use Zlodes\PrometheusClient\Exception\MetricNotFoundException;
use Zlodes\PrometheusClient\Metric\Counter;
use Zlodes\PrometheusClient\Metric\Gauge;
use Zlodes\PrometheusClient\Metric\Histogram;
use Zlodes\PrometheusClient\Metric\Summary;
use Zlodes\PrometheusClient\Registry\Registry;
use Zlodes\PrometheusClient\Storage\Storage;

class CollectorFactoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testCounterFound(): void
    {
        $factory = new CollectorFactory(
            $registryMock = Mockery::mock(Registry::class),
            Mockery::mock(Storage::class),
            new NullLogger(),
        );

        $registryMock
            ->expects('getCounter')
            ->with('counter_name')
            ->andReturn(new Counter('counter_name', 'Foo'));

        $factory->counter('counter_name');
    }

    public function testCounterNotFound(): void
    {
        $factory = new CollectorFactory(
            $registryMock = Mockery::mock(Registry::class),
            Mockery::mock(Storage::class),
            new NullLogger(),
        );

        $registryMock
            ->expects('getCounter')
            ->with('counter_name')
            ->andThrow(new MetricNotFoundException());

        $this->expectException(MetricNotFoundException::class);

        $factory->counter('counter_name');
    }

    public function testGaugeFound(): void
    {
        $factory = new CollectorFactory(
            $registryMock = Mockery::mock(Registry::class),
            Mockery::mock(Storage::class),
            new NullLogger(),
        );

        $registryMock
            ->expects('getGauge')
            ->with('gauge_name')
            ->andReturn(new Gauge('gauge_name', 'Foo'));

        $factory->gauge('gauge_name');
    }

    public function testGaugeNotFound(): void
    {
        $factory = new CollectorFactory(
            $registryMock = Mockery::mock(Registry::class),
            Mockery::mock(Storage::class),
            new NullLogger(),
        );

        $registryMock
            ->expects('getGauge')
            ->with('gauge_name')
            ->andThrow(new MetricNotFoundException());

        $this->expectException(MetricNotFoundException::class);

        $factory->gauge('gauge_name');
    }

    public function testHistogramFound(): void
    {
        $factory = new CollectorFactory(
            $registryMock = Mockery::mock(Registry::class),
            Mockery::mock(Storage::class),
            new NullLogger(),
        );

        $registryMock
            ->expects('getHistogram')
            ->with('histogram_name')
            ->andReturn(new Histogram('histogram_name', 'Foo'));

        $factory->histogram('histogram_name');
    }

    public function testHistogramNotFound(): void
    {
        $factory = new CollectorFactory(
            $registryMock = Mockery::mock(Registry::class),
            Mockery::mock(Storage::class),
            new NullLogger(),
        );

        $registryMock
            ->expects('getHistogram')
            ->with('histogram_name')
            ->andThrow(new MetricNotFoundException());

        $this->expectException(MetricNotFoundException::class);

        $factory->histogram('histogram_name');
    }

    public function testSummaryFound(): void
    {
        $factory = new CollectorFactory(
            $registryMock = Mockery::mock(Registry::class),
            Mockery::mock(Storage::class),
            new NullLogger(),
        );

        $registryMock
            ->expects('getSummary')
            ->with('summary_name')
            ->andReturn(new Summary('summary_name', 'Foo'));

        $factory->summary('summary_name');
    }

    public function testSummaryNotFound(): void
    {
        $factory = new CollectorFactory(
            $registryMock = Mockery::mock(Registry::class),
            Mockery::mock(Storage::class),
            new NullLogger(),
        );

        $registryMock
            ->expects('getSummary')
            ->with('summary_name')
            ->andThrow(new MetricNotFoundException());

        $this->expectException(MetricNotFoundException::class);

        $factory->summary('summary_name');
    }
}
