<?php

declare(strict_types=1);

namespace Zlodes\PrometheusClient\Collector;

final class Timer
{
    private float $startedAt;

    /**
     * @internal Zlodes\PrometheusClient\Collector
     */
    public function __construct(
        private readonly UpdatableCollector $collector,
    ) {
        $this->startedAt = microtime(true);
    }

    public function stop(): void
    {
        $elapsed = microtime(true) - $this->startedAt;

        $this->collector->update($elapsed);
    }
}
