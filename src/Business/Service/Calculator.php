<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Business\Service;

use Alexsoft\FeeCalculator\Business\Contract\FeeStructureRepository;
use Alexsoft\FeeCalculator\Business\Domain\TermMonths;
use Brick\Money\Money;

final readonly class Calculator
{
    public function __construct(
        private FeeStructureRepository $repository,
        private FeeRoundingService $roundingService,
    ) {}

    public function calculate(Money $amount, TermMonths $term): Money
    {
        $feeStructure = $this->repository->forTerm($term);

        $initialFee = $feeStructure->feeFor($amount);

        return $this->roundingService->roundFeeToMakeTotalDivisible($amount, $initialFee);
    }
}
