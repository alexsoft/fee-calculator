<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Tests\Unit\Business\Service;

use Alexsoft\FeeCalculator\Business\Contract\FeeStructureRepository;
use Alexsoft\FeeCalculator\Business\Domain\AmountFeePair;
use Alexsoft\FeeCalculator\Business\Domain\FeeStructure;
use Alexsoft\FeeCalculator\Business\Domain\TermMonths;
use Alexsoft\FeeCalculator\Business\Service\Calculator;
use Alexsoft\FeeCalculator\Business\Service\FeeRoundingService;
use Brick\Math\BigNumber;
use Brick\Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(Calculator::class)]
final class CalculatorTest extends TestCase
{
    private Calculator $sut;

    private FeeStructureRepository&MockObject $feeStructureRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->feeStructureRepository = $this->createMock(FeeStructureRepository::class);

        $this->sut = new Calculator($this->feeStructureRepository, new FeeRoundingService(BigNumber::of(5)));
    }

    #[Test]
    public function it_calculates_properly(): void
    {
        $this->feeStructureRepository
            ->expects($this->once())
            ->method('forTerm')
            ->with(TermMonths::Twelve)
            ->willReturn(
                new FeeStructure(
                    new AmountFeePair(
                        Money::of(1000, 'EUR'),
                        Money::of(15, 'EUR'),
                    ),
                    new AmountFeePair(
                        Money::of(2000, 'EUR'),
                        Money::of(40, 'EUR'),
                    ),
                ),
            );

        $this->assertEquals(
            Money::of(20, 'EUR')->getAmount(),
            $this->sut
                ->calculate(
                    Money::of(1100, 'EUR'),
                    TermMonths::Twelve,
                )
                ->getAmount(),
        );
    }
}
