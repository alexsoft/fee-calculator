<?php

declare(strict_types=1);

namespace Alexsoft\Fee\Infrastructure\Repository;

use Alexsoft\Fee\Application\EurMoney;
use Alexsoft\Fee\Business\Contract\FeeStructureRepository;
use Alexsoft\Fee\Business\Domain\AmountFeePair;
use Alexsoft\Fee\Business\Domain\FeeStructure;
use Alexsoft\Fee\Business\Domain\TermMonths;

final readonly class InMemoryFeeStructureRepository implements FeeStructureRepository
{
    /**
     * @param array<value-of<TermMonths>, list<array{float|int, float|int}>> $breakpoints
     */
    public function __construct(private array $breakpoints) {}

    public function forTerm(TermMonths $termMonths): FeeStructure
    {
        return new FeeStructure(
            ...array_map(
                static fn($pair) => new AmountFeePair(EurMoney::of($pair[0]), EurMoney::of($pair[1])),
                $this->breakpoints[$termMonths->value],
            ),
        );
    }
}
