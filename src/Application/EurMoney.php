<?php

declare(strict_types=1);

namespace Alexsoft\Fee\Application;

use Brick\Money\Money;

final readonly class EurMoney
{
    public static function of(int|float|string $amount): Money
    {
        return Money::of($amount, 'EUR');
    }
}
