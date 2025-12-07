<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Application;

use Alexsoft\FeeCalculator\Business\Service\Calculator;
use Brick\Money\Money;

final readonly class CalculateFeeHandler
{
    public function __construct(
        private Calculator $calculator,
    ) {}

    public function handle(CalculateFeeCommand $command): Money {
        return $this->calculator->calculate(
            $command->amount,
            $command->term,
        );
    }
}
