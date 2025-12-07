<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Business\Domain;

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

        $amountFeePairsList = array_values($amountFeePairs);

        // Validate sorting
        for ($i = 1; $i < count($amountFeePairsList); $i++) {
            if ($amountFeePairsList[$i]->amount->isLessThanOrEqualTo($amountFeePairsList[$i - 1]->amount)) {
                throw new InvalidArgumentException('AmountFeePairs must be sorted by amount in ascending order');
            }
        }

        $this->amountFeePairs = $amountFeePairsList;
    }

    public function feeFor(Money $amount): Money
    {
        foreach ($this->amountFeePairs as $i => $amountFeePair) {
            if ($amountFeePair->amount->isEqualTo($amount)) {
                return $amountFeePair->fee;
            }

            if ($amountFeePair->amount->isGreaterThan($amount)) {
                if ($i === 0) {
                    throw new RuntimeException('Amount outside of valid range');
                }

                return $this->interpolate($amount, $this->amountFeePairs[$i - 1], $amountFeePair);
            }
        }

        throw new RuntimeException('Amount outside of valid range');
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
        // Calculate: ratio = (amount - lowerAmount) / (upperAmount - lowerAmount)
        $amountRange = $upperPair->amount->minus($lowerPair->amount);
        $amountOffset = $amount->minus($lowerPair->amount);
        $ratio = $amountOffset->dividedBy($amountRange->getAmount());

        // Calculate: fee = lowerFee + ratio * (upperFee - lowerFee)
        $feeRange = $upperPair->fee->minus($lowerPair->fee);
        $feeOffset = $ratio->multipliedBy($feeRange->getAmount());

        return $lowerPair->fee->plus($feeOffset);
    }
}
