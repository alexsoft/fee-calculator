<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Business\Service;

use Alexsoft\FeeCalculator\Business\Contract\FeeStructureRepository;
use Alexsoft\FeeCalculator\Business\Domain\TermMonths;
use Brick\Money\Money;
use RuntimeException;

final readonly class Calculator
{
    public function __construct(
        private FeeStructureRepository $repository,
        private FeeRoundingService $roundingService,
    ) {}

    public function calculate(Money $amount, TermMonths $term): Money
    {
        $feeStructure = $this->repository->forTerm($term);

        if (!$feeStructure->canHandleAmount($amount)) {
            throw new RuntimeException("Provided amount [{$amount->getAmount()}] is out of range of fee structure.");
        }

        $rawFee = $feeStructure->feeFor($amount);

        return $this->roundingService->roundFeeToMakeTotalDivisible($amount, $rawFee);
    }
}
