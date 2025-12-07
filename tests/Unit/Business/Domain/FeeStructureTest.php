<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Tests\Unit\Business\Domain;

use Alexsoft\FeeCalculator\Business\Domain\AmountFeePair;
use Alexsoft\FeeCalculator\Business\Domain\FeeStructure;
use Brick\Money\Money;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(FeeStructure::class)]
final class FeeStructureTest extends TestCase
{
    #[Test]
    #[DataProvider('invalidAmountFeePairsDataProvider')]
    public function it_validates_structure_of_amount_fee_pairs(array $pairs, string $expectedMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        new FeeStructure(...$pairs);
    }

    #[Test]
    #[DataProvider('outOfRangeAmountDataProvider')]
    public function it_throws_exception_for_amount_which_is_out_of_range(
        array $pairs,
        Money $amount,
        string $expectedMessage,
    ): void {
        $sut = new FeeStructure(...$pairs);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expectedMessage);

        $sut->feeFor($amount);
    }

    #[Test]
    #[DataProvider('feeForAmountDataProvider')]
    public function it_can_get_fee_for_amount(
        array $pairs,
        Money $amount,
        Money $expectedFee,
    ): void {
        $sut = new FeeStructure(...$pairs);

        $this->assertEquals(
            $expectedFee,
            $sut->feeFor($amount),
        );
    }

    public static function invalidAmountFeePairsDataProvider(): iterable
    {
        yield 'Empty array' => [
            'pairs' => [],
            'expectedMessage' => 'FeeStructure must not be empty',
        ];

        yield 'Unsorted amount fee pairs, first 2' => [
            'pairs' => [
                new AmountFeePair(Money::of(2000, 'EUR'), Money::of(20, 'EUR')),
                new AmountFeePair(Money::of(1000, 'EUR'), Money::of(10, 'EUR')),
            ],
            'expectedMessage' => 'AmountFeePairs must be sorted by amount in ascending order',
        ];

        yield 'Unsorted amount fee pairs, last 2' => [
            'pairs' => [
                new AmountFeePair(Money::of(1000, 'EUR'), Money::of(10, 'EUR')),
                new AmountFeePair(Money::of(2000, 'EUR'), Money::of(20, 'EUR')),
                new AmountFeePair(Money::of(3000, 'EUR'), Money::of(30, 'EUR')),
                new AmountFeePair(Money::of(5000, 'EUR'), Money::of(50, 'EUR')),
                new AmountFeePair(Money::of(4000, 'EUR'), Money::of(40, 'EUR')),
            ],
            'expectedMessage' => 'AmountFeePairs must be sorted by amount in ascending order',
        ];
    }

    public static function outOfRangeAmountDataProvider(): iterable
    {
        yield 'Amount is lower than fee structure' => [
            'pairs' => [
                new AmountFeePair(Money::of(1000, 'EUR'), Money::of(10, 'EUR')),
                new AmountFeePair(Money::of(2000, 'EUR'), Money::of(20, 'EUR')),
            ],
            'amount' => Money::of(999.99, 'EUR'),
            'expectedMessage' => "Provided amount [999.99] is out of range of fee structure.",
        ];

        yield 'Amount is higher than fee structure' => [
            'pairs' => [
                new AmountFeePair(Money::of(1000, 'EUR'), Money::of(10, 'EUR')),
                new AmountFeePair(Money::of(2000, 'EUR'), Money::of(20, 'EUR')),
            ],
            'amount' => Money::of(2000.01, 'EUR'),
            'expectedMessage' => "Provided amount [2000.01] is out of range of fee structure.",
        ];
    }

    public static function feeForAmountDataProvider(): iterable
    {
        $pairs = [
            new AmountFeePair(Money::of(1000, 'EUR'), Money::of(50, 'EUR')),
            new AmountFeePair(Money::of(2000, 'EUR'), Money::of(90, 'EUR')),
            new AmountFeePair(Money::of(3000, 'EUR'), Money::of(100, 'EUR')),
        ];

        yield 'first item is exact result' => [
            'pairs' => $pairs,
            'amount' => Money::of(1000, 'EUR'),
            'expectedFee' => Money::of(50, 'EUR'),
        ];
        yield 'last item is exact result' => [
            'pairs' => $pairs,
            'amount' => Money::of(3000, 'EUR'),
            'expectedFee' => Money::of(100, 'EUR'),
        ];
        yield 'interpolated result, exactly in between' => [
            'pairs' => $pairs,
            'amount' => Money::of(1500, 'EUR'),
            'expectedFee' => Money::of(70, 'EUR'),
        ];
        yield 'interpolated result, not in between' => [
            'pairs' => $pairs,
            'amount' => Money::of(1900, 'EUR'),
            'expectedFee' => Money::of(86, 'EUR'),
        ];
    }
}
