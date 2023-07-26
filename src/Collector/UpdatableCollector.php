<?php

declare(strict_types=1);

namespace Zlodes\PrometheusClient\Collector;

interface UpdatableCollector
{
    public function update(int|float $value): void;
}
