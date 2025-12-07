<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Business\Domain;

use Brick\Money\Money;

/**
 * @codeCoverageIgnore
 */
final readonly class AmountFeePair
{
    public function __construct(
        public Money $amount,
        public Money $fee,
    ) {}

}
