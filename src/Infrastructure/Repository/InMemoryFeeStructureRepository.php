<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Infrastructure\Repository;

use Alexsoft\FeeCalculator\Business\Contract\FeeStructureRepository;
use Alexsoft\FeeCalculator\Business\Domain\AmountFeePair;
use Alexsoft\FeeCalculator\Business\Domain\FeeStructure;
use Alexsoft\FeeCalculator\Business\Domain\TermMonths;
use Brick\Money\Money;
use InvalidArgumentException;

final readonly class InMemoryFeeStructureRepository implements FeeStructureRepository
{
    /**
     * @param array<value-of<TermMonths>, list<array{float|int, float|int}>> $breakpoints
     */
    public function __construct(private array $breakpoints)
    {
        foreach (TermMonths::cases() as $term) {
            if (!isset($this->breakpoints[$term->value])) {
                throw new InvalidArgumentException(
                    "Missing breakpoints for term {$term->value}",
                );
            }

            if (
                !is_array($this->breakpoints[$term->value]) // @phpstan-ignore booleanNot.alwaysFalse (Must verify structure to be sure)
                || $this->breakpoints[$term->value] === []
            ) {
                throw new InvalidArgumentException(
                    "Invalid breakpoints for term {$term->value}",
                );
            }
        }
    }

    public function forTerm(TermMonths $termMonths): FeeStructure
    {
        $amountFeePairs = array_map(
            static fn(array $pair) => new AmountFeePair(
                Money::of($pair[0], 'EUR'),
                Money::of($pair[1], 'EUR'),
            ),
            $this->breakpoints[$termMonths->value],
        );

        return new FeeStructure(...$amountFeePairs);
    }
}
