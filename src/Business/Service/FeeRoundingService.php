<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Business\Service;

use Brick\Math\BigNumber;
use Brick\Money\Money;

final readonly class FeeRoundingService
{
    public function __construct(
        private BigNumber $divisibleBy,
    ) {}

    public function roundFeeToMakeTotalDivisible(Money $amount, Money $fee): Money
    {
        $sum = $amount->plus($fee);

        $remainder = $sum->getAmount()->remainder($this->divisibleBy);

        if ($remainder->isZero()) {
            return $fee;
        }

        return $fee
            ->plus($this->divisibleBy)
            ->minus($remainder);
    }
}
