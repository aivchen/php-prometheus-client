<?php

declare(strict_types=1);

namespace Zlodes\PrometheusExporter\Collector\ByType;

use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;
use Zlodes\PrometheusExporter\Collector\WithLabels;
use Zlodes\PrometheusExporter\Exceptions\StorageWriteException;
use Zlodes\PrometheusExporter\MetricTypes\Counter;
use Zlodes\PrometheusExporter\Storage\DTO\MetricNameWithLabels;
use Zlodes\PrometheusExporter\Storage\DTO\MetricValue;
use Zlodes\PrometheusExporter\Storage\Storage;

final class CounterCollector
{
    use WithLabels;

    public function __construct(
        private readonly Counter $counter,
        private readonly Storage $storage,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param positive-int|float $value
     *
     * @return void
     */
    public function increment(int|float $value = 1): void
    {
        Assert::true($value > 0, 'Increment value of Counter metric MUST be positive');

        $counter = $this->counter;
        $labels = $this->composeLabels();

        try {
            $this->storage->incrementValue(
                new MetricValue(
                    new MetricNameWithLabels($counter->getName(), $labels),
                    $value
                )
            );
        } catch (StorageWriteException $e) {
            $this->logger->error("Cannot increment counter {$counter->getName()}: $e");
        }
    }

    /**
     * @return array<non-empty-string, non-empty-string>
     */
    private function composeLabels(): array
    {
        return array_merge($this->counter->getInitialLabels(), $this->labels);
    }
}
