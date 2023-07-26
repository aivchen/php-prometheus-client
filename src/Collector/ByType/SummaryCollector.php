<?php

declare(strict_types=1);

namespace Zlodes\PrometheusClient\Collector\ByType;

use Psr\Log\LoggerInterface;
use Zlodes\PrometheusClient\Collector\Timer;
use Zlodes\PrometheusClient\Collector\UpdatableCollector;
use Zlodes\PrometheusClient\Collector\WithLabels;
use Zlodes\PrometheusClient\Exception\StorageWriteException;
use Zlodes\PrometheusClient\Metric\Summary;
use Zlodes\PrometheusClient\Storage\DTO\MetricNameWithLabels;
use Zlodes\PrometheusClient\Storage\DTO\MetricValue;
use Zlodes\PrometheusClient\Storage\Storage;

/**
 * @final
 */
final class SummaryCollector implements UpdatableCollector
{
    use WithLabels;

    /**
     * @internal Zlodes\PrometheusClient\Collector
     */
    public function __construct(
        private readonly Summary $summary,
        private readonly Storage $storage,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function update(float|int $value): void
    {
        $summary = $this->summary;
        $labels = $this->composeLabels();

        try {
            $this->storage->persistSummary(
                new MetricValue(
                    new MetricNameWithLabels($summary->getName(), $labels),
                    $value,
                )
            );
        } catch (StorageWriteException $e) {
            $this->logger->error("Cannot persist Summary {$summary->getName()}: $e");
        }
    }

    public function startTimer(): Timer
    {
        return new Timer($this);
    }

    /**
     * @return array<non-empty-string, non-empty-string>
     */
    private function composeLabels(): array
    {
        return array_merge($this->summary->getInitialLabels(), $this->labels);
    }
}
