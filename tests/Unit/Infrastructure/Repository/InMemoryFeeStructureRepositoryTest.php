<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Tests\Unit\Infrastructure\Repository;

use Alexsoft\FeeCalculator\Business\Domain\AmountFeePair;
use Alexsoft\FeeCalculator\Business\Domain\FeeStructure;
use Alexsoft\FeeCalculator\Business\Domain\TermMonths;
use Alexsoft\FeeCalculator\Infrastructure\Repository\InMemoryFeeStructureRepository;
use Brick\Money\Money;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InMemoryFeeStructureRepository::class)]
final class InMemoryFeeStructureRepositoryTest extends TestCase
{
    /**
     * @param array<value-of<TermMonths>, list<array{float|int, float|int}>> $breakpoints
     */
    #[Test]
    #[DataProvider('invalidBreakpointsDataProvider')]
    public function it_throws_exception_if_provided_invalid_breakpoints(
        array $breakpoints,
        string $expectedMessage,
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        new InMemoryFeeStructureRepository($breakpoints);
    }

    #[Test]
    public function it_gets_structure_for_term(): void
    {
        $structure12Months = [
            [1_000, 50],
            [2_000, 70],
        ];
        $structure24Months = [
            [1_200, 35],
            [2_100, 49],
        ];

        $sut = new InMemoryFeeStructureRepository(
            [
                12 => $structure12Months,
                24 => $structure24Months,
            ],
        );

        $this->assertEquals(
            new FeeStructure(
                new AmountFeePair(Money::of(1_000, 'EUR'), Money::of(50, 'EUR')),
                new AmountFeePair(Money::of(2_000, 'EUR'), Money::of(70, 'EUR')),
            ),
            $sut->forTerm(TermMonths::Twelve),
        );
        $this->assertEquals(
            new FeeStructure(
                new AmountFeePair(Money::of(1_200, 'EUR'), Money::of(35, 'EUR')),
                new AmountFeePair(Money::of(2_100, 'EUR'), Money::of(49, 'EUR')),
            ),
        $sut->forTerm(TermMonths::TwentyFour));
    }

    /**
     * @return iterable<string, array{breakpoints: array<int, mixed>, expectedMessage: string}>
     */
    public static function invalidBreakpointsDataProvider(): iterable
    {
        yield 'Missing 12 months breakpoints' => [
            'breakpoints' => [
                24 => [
                    [1_000, 50],
                    [2_000, 90],
                ],
            ],
            'expectedMessage' => 'Missing breakpoints for term 12',
        ];
        yield 'Missing 24 months breakpoints' => [
            'breakpoints' => [
                12 => [
                    [1_000, 50],
                    [2_000, 90],
                ],
            ],
            'expectedMessage' => 'Missing breakpoints for term 24',
        ];
        yield 'Invalid breakpoints for term 12, string' => [
            'breakpoints' => [
                12 => '1000,50',
                24 => [
                    [1_000, 50],
                    [2_000, 90],
                ],
            ],
            'expectedMessage' => 'Invalid breakpoints for term 12',
        ];
        yield 'Invalid breakpoints for term 12, empty array' => [
            'breakpoints' => [
                12 => [],
                24 => [
                    [1_000, 50],
                    [2_000, 90],
                ],
            ],
            'expectedMessage' => 'Invalid breakpoints for term 12',
        ];
        yield 'Invalid breakpoints for term 24, string' => [
            'breakpoints' => [
                12 => [
                    [1_000, 50],
                    [2_000, 90],
                ],
                24 => '1000,50',
            ],
            'expectedMessage' => 'Invalid breakpoints for term 24',
        ];
        yield 'Invalid breakpoints for term 24, empty array' => [
            'breakpoints' => [
                12 => [
                    [1_000, 50],
                    [2_000, 90],
                ],
                24 => [],
            ],
            'expectedMessage' => 'Invalid breakpoints for term 24',
        ];
    }
}
