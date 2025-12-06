<?php

declare(strict_types=1);

namespace Alexsoft\Fee\Business\Contract;

use Alexsoft\Fee\Business\Domain\FeeStructure;
use Alexsoft\Fee\Business\Domain\TermMonths;

interface FeeStructureRepository
{
    public function forTerm(TermMonths $termMonths): FeeStructure;
}
