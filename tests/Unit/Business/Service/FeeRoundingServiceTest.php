<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Tests\Unit\Business\Service;

use Alexsoft\FeeCalculator\Business\Service\FeeRoundingService;
use Brick\Math\BigNumber;
use Brick\Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FeeRoundingService::class)]
final class FeeRoundingServiceTest extends TestCase
{
    #[Test]
    public function it_returns_original_fee_when_total_is_already_divisible(): void
    {
        $service = new FeeRoundingService(BigNumber::of(100));

        $amount = Money::of(250, 'USD');
        $fee = Money::of(50, 'USD');

        $result = $service->roundFeeToMakeTotalDivisible($amount, $fee);

        $this->assertTrue($result->isEqualTo($fee));
        $this->assertEquals('50.00', $result->getAmount()->toScale(2));
    }

    #[Test]
    public function it_rounds_fee_up_when_total_has_remainder(): void
    {
        $service = new FeeRoundingService(BigNumber::of(100));

        $amount = Money::of(255, 'USD');
        $fee = Money::of(50, 'USD');

        $result = $service->roundFeeToMakeTotalDivisible($amount, $fee);

        // Total is 305, remainder is 5, so fee should increase by (100 - 5) = 95
        // New fee: 50 + 95 = 145
        $this->assertEquals('145.00', $result->getAmount()->toScale(2));

        $newTotal = $amount->plus($result);
        $this->assertTrue($newTotal->getAmount()->remainder(BigNumber::of(100))->isZero());
    }

    #[Test]
    public function it_rounds_fee_with_small_remainder(): void
    {
        $service = new FeeRoundingService(BigNumber::of(100));

        $amount = Money::of(299, 'USD');
        $fee = Money::of(1, 'USD');

        $result = $service->roundFeeToMakeTotalDivisible($amount, $fee);

        // Total is 300, which is already divisible by 100
        $this->assertEquals('1.00', $result->getAmount()->toScale(2));
    }

    #[Test]
    public function it_rounds_fee_with_large_remainder(): void
    {
        $service = new FeeRoundingService(BigNumber::of(100));

        $amount = Money::of(201, 'USD');
        $fee = Money::of(1, 'USD');

        $result = $service->roundFeeToMakeTotalDivisible($amount, $fee);

        // Total is 202, remainder is 2, so fee should increase by (100 - 2) = 98
        // New fee: 1 + 98 = 99
        $this->assertEquals('99.00', $result->getAmount()->toScale(2));
    }

    #[Test]
    public function it_works_with_different_divisor(): void
    {
        $service = new FeeRoundingService(BigNumber::of(50));

        $amount = Money::of(123, 'USD');
        $fee = Money::of(10, 'USD');

        $result = $service->roundFeeToMakeTotalDivisible($amount, $fee);

        // Total is 133, remainder is 33, so fee should increase by (50 - 33) = 17
        // New fee: 10 + 17 = 27
        $this->assertEquals('27.00', $result->getAmount()->toScale(2));

        $newTotal = $amount->plus($result);
        $this->assertTrue($newTotal->getAmount()->remainder(BigNumber::of(50))->isZero());
    }

    #[Test]
    public function it_works_with_decimal_amounts(): void
    {
        $service = new FeeRoundingService(BigNumber::of(100));

        $amount = Money::of('12.34', 'USD');
        $fee = Money::of('5.67', 'USD');

        $result = $service->roundFeeToMakeTotalDivisible($amount, $fee);

        // Total is 18.01, remainder is 18.01, so fee should increase by (100 - 18.01) = 81.99
        // New fee: 5.67 + 81.99 = 87.66
        $this->assertEquals('87.66', $result->getAmount()->toScale(2));

        $newTotal = $amount->plus($result);
        $this->assertTrue($newTotal->getAmount()->remainder(BigNumber::of(100))->isZero());
    }
}
