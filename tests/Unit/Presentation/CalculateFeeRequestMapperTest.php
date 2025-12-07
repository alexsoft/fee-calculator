<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Tests\Unit\Presentation;

use Alexsoft\FeeCalculator\Application\CalculateFeeCommand;
use Alexsoft\FeeCalculator\Business\Domain\TermMonths;
use Alexsoft\FeeCalculator\Presentation\CalculateFeeCommandMapper;
use Brick\Money\Money;
use InvalidArgumentException;
use NumberFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CalculateFeeCommandMapper::class)]
final class CalculateFeeRequestMapperTest extends TestCase
{
    #[Test]
    #[DataProvider('invalidTermsDataProvider')]
    public function it_throws_exception_for_invalid_term(string $termString): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid term. Got [{$termString}]. Expected one of: 12, 24");

        $sut = $this->createSut();

        $sut->map('1000', $termString);
    }

    #[Test]
    #[DataProvider('validDataProvider')]
    public function it_maps_properly(
        string $amountString,
        string $termString,
        Money $expectedAmount,
        TermMonths $expectedTermMonths,
    ): void
    {
        $sut = $this->createSut();

        $this->assertEquals(
            new CalculateFeeCommand(
                $expectedAmount,
                $expectedTermMonths,
            ),
            $sut->map($amountString, $termString),
        );
    }

    private function createSut(): CalculateFeeCommandMapper
    {
        return new CalculateFeeCommandMapper(new NumberFormatter('en_LU', NumberFormatter::DECIMAL));
    }

    /**
     * @return iterable<array<numeric-string>>
     */
    public static function invalidTermsDataProvider(): iterable
    {
        yield ['1'];
        yield ['2'];
        yield ['2.22'];
        yield ['5'];
        yield ['12.01'];
        yield ['12.99'];
    }

    /**
     * @return iterable<string, array{string, numeric-string, Money, TermMonths}>
     */
    public static function validDataProvider(): iterable
    {
        yield '11200 - 24' => [
            '11200',
            '24',
            Money::of(11200, 'EUR'),
            TermMonths::TwentyFour,
        ];
        yield '9511.57 - 12' => [
            '9,511.57',
            '12',
            Money::of(9511.57, 'EUR'),
            TermMonths::Twelve,
        ];
   }
}
