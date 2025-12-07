<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Application;

use Alexsoft\FeeCalculator\Business\Domain\TermMonths;
use Brick\Money\Money;

final readonly class CalculateFeeCommand
{
    public function __construct(
        public Money $amount,
        public TermMonths $term,
    ) {}
}
