<?php

declare(strict_types=1);

namespace Alexsoft\Fee\Business\Domain;

use Brick\Money\Money;
use InvalidArgumentException;
use RuntimeException;

final readonly class FeeStructure
{
    /** @var non-empty-list<AmountFeePair> */
    private array $amountFeePairs;

    public function __construct(AmountFeePair ...$amountFeePairs)
    {
        if ($amountFeePairs === []) {
            throw new InvalidArgumentException('FeeStructure must not be empty');
        }

        $this->amountFeePairs = array_values($amountFeePairs);
    }

    public function feeFor(Money $amount): Money
    {
        $lowerPair = null;
        $upperPair = null;

        foreach ($this->amountFeePairs as $amountFeePair) {
            if ($amountFeePair->amount->isEqualTo($amount)) {
                return $amountFeePair->fee;
            }

            if ($amountFeePair->amount->isLessThan($amount)) {
                $lowerPair = $amountFeePair;
            }

            if ($amountFeePair->amount->isGreaterThan($amount)) {
                $upperPair = $amountFeePair;
                break;
            }
        }

        if (is_null($lowerPair) || is_null($upperPair)) {
            throw new RuntimeException('Amount outside of valid range');
        }

        return $this->interpolate($amount, $lowerPair, $upperPair);
    }

    public function canHandleAmount(Money $amount): bool
    {
        return $amount->isGreaterThanOrEqualTo($this->getSmallestAmount())
            && $amount->isLessThanOrEqualTo($this->getBiggestAmount());
    }

    private function getSmallestAmount(): Money
    {
        return $this->amountFeePairs[array_key_first($this->amountFeePairs)]->amount;
    }

    private function getBiggestAmount(): Money
    {
        return $this->amountFeePairs[array_key_last($this->amountFeePairs)]->amount;
    }

    private function interpolate(Money $amount, AmountFeePair $lowerPair, AmountFeePair $upperPair): Money
    {
        $ratio = $amount
            ->minus($lowerPair->amount)
            ->dividedBy(
                $upperPair->amount->minus($lowerPair->amount)->getAmount(),
            );

        return $lowerPair->fee
            ->plus(
                $ratio->multipliedBy($upperPair->fee->minus($lowerPair->fee)->getAmount()),
            );
    }
}
