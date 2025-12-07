<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Tests\Unit\Business\Domain;

use Alexsoft\FeeCalculator\Business\Domain\TermMonths;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TermMonths::class)]
final class TermMonthsTest extends TestCase
{
    #[Test]
    public function it_provides_possible_values(): void
    {
        $this->assertEquals(
            [12, 24],
            TermMonths::getPossibleValues(),
        );
    }
}
