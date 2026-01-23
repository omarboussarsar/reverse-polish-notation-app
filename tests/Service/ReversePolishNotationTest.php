<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ReversePolishNotation;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ReversePolishNotationTest extends TestCase
{
    public function testAdditionOnIntegers(): void
    {
        $service = new ReversePolishNotation();

        self::assertSame(7.0, $service->evaluate('3 4 +'));
    }

    public function testSubtractionOnIntegers(): void
    {
        $service = new ReversePolishNotation();

        self::assertSame(-1.0, $service->evaluate('3 4 -'));
    }

    public function testMultiplicationOnIntegers(): void
    {
        $service = new ReversePolishNotation();

        self::assertSame(42.0, $service->evaluate('6 7 *'));
    }

    public function testDivisionOnIntegers(): void
    {
        $service = new ReversePolishNotation();

        self::assertSame(3.0, $service->evaluate('9 3 /'));
    }

    public function testDivisionByZeroThrows(): void
    {
        $service = new ReversePolishNotation();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Division by zero.');

        $service->evaluate('5 0 /');
    }

    public function testIntegerOverflowThrows(): void
    {
        $service = new ReversePolishNotation();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Integer overflow.');

        $service->evaluate(PHP_INT_MAX . ' 1 +');
    }

    public function testPowerFunction(): void
    {
        $service = new ReversePolishNotation();

        self::assertSame(8.0, $service->evaluate('2 3 ^'));
    }

    public function testFactorialFunction(): void
    {
        $service = new ReversePolishNotation();

        self::assertSame(120.0, $service->evaluate('5 !'));
    }

    public function testDivisionWithRemainder(): void
    {
        $service = new ReversePolishNotation();

        self::assertSame(1.0, $service->evaluate('7 3 mod'));
    }

    public function testModuloByZeroThrows(): void
    {
        $service = new ReversePolishNotation();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Division by zero.');

        $service->evaluate('7 0 mod');
    }

    public function testUnknownOperatorThrows(): void
    {
        $service = new ReversePolishNotation();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown operator: foo');

        $service->evaluate('1 2 foo');
    }

    public function testMultipleOperators(): void
    {
        $service = new ReversePolishNotation();

        self::assertSame(14.0, $service->evaluate('5 1 2 + 4 * + 3 -'));
    }
}
