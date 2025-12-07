<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Business\Contract;

use Alexsoft\FeeCalculator\Business\Domain\FeeStructure;
use Alexsoft\FeeCalculator\Business\Domain\TermMonths;

interface FeeStructureRepository
{
    public function forTerm(TermMonths $termMonths): FeeStructure;
}
