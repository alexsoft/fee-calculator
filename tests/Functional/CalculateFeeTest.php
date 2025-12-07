<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Tests\Functional;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversNothing]
final class CalculateFeeTest extends TestCase
{
    #[Test]
    public function it_prints_to_stdout_upon_success(): void
    {
        [$stdout, $stderr, $exitCode] = $this->runAndGetStdoutAndStderr('3200', '24');

        $this->assertEquals("130.00\n", $stdout);
        $this->assertEquals(0, $exitCode, 'Exit code was not 0');
        $this->assertEmpty($stderr, "Script produced errors: $stderr");
    }

    #[Test]
    public function it_prints_to_stderr_upon_failure(): void
    {
        [$stdout, $stderr, $exitCode] = $this->runAndGetStdoutAndStderr('999', '12');

        $this->assertEmpty($stdout, "Script produced standard output: {$stdout}");
        $this->assertGreaterThan(0, $exitCode, 'Exit code was 0');
        $this->assertEquals('Provided amount [999.00] is out of range of fee structure.', $stderr, 'Wrong text in stderr');
    }

    /**
     * @return array{string, string, int}
     */
    private function runAndGetStdoutAndStderr(string $amount, string $term): array
    {
        $cmd = 'php ' . escapeshellarg(__DIR__ . '/../../bin/calculate-fee') . " {$amount} {$term}";

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($cmd, $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new RuntimeException('Failed to start process');
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        return [$stdout, $stderr, $exitCode];
    }
}

