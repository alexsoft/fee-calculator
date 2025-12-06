<?php

declare(strict_types=1);

namespace Alexsoft\Fee\Business\Domain;

use Brick\Money\Money;

final readonly class AmountFeePair
{
    public function __construct(
        public Money $amount,
        public Money $fee,
    ) {}

}
