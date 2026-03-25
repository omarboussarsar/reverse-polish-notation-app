<?php

declare(strict_types=1);

namespace App\Service;

use InvalidArgumentException;

final class ReversePolishNotation
{
    public function evaluate(string $expression): float
    {
        $expression = trim($expression);
        if ($expression === '') {
            throw new InvalidArgumentException('Expression cannot be empty.');
        }

        $stack = [];
        $parenthesisLevel = 0;
        preg_match_all('/\(|\)|[^\s()]+/', $expression, $matches);
        $tokens = $matches[0] ?? [];

        foreach ($tokens as $token) {
            if ($token === '(') {
                ++$parenthesisLevel;
                continue;
            }

            if ($token === ')') {
                if ($parenthesisLevel === 0) {
                    throw new InvalidArgumentException('Mismatched parentheses.');
                }

                --$parenthesisLevel;
                continue;
            }

            if (is_numeric($token)) {
                if ($token >= PHP_INT_MAX) {
                    throw new InvalidArgumentException('Integer overflow.');
                }

                $stack[] = (float) $token;
                continue;
            }

            switch ($token) {
                case '+':
                    [$left, $right] = $this->popBinaryOperands($stack);
                    $stack[] = $left + $right;
                    break;
                case '-':
                    [$left, $right] = $this->popBinaryOperands($stack);
                    $stack[] = $left - $right;
                    break;
                case '*':
                    [$left, $right] = $this->popBinaryOperands($stack);
                    $stack[] = $left * $right;
                    break;
                case '/':
                    [$left, $right] = $this->popBinaryOperands($stack);
                    if (0 == $right) {
                        throw new InvalidArgumentException('Division by zero.');
                    }
                    $stack[] = $left / $right;
                    break;
                case '^':
                    [$left, $right] = $this->popBinaryOperands($stack);
                    $stack[] = $left ** $right;
                    break;
                case '!':
                    $right = $this->popUnaryOperand($stack);
                    $factorial = 1;
                    for ($i = 1; $i <= (int) $right; $i++) {
                        $factorial *= $i;
                    }
                    $stack[] = (float) $factorial;
                    break;
                case 'mod':
                    [$left, $right] = $this->popBinaryOperands($stack);
                    if (0 == $right) {
                        throw new InvalidArgumentException('Division by zero.');
                    }
                    $stack[] = $left % $right;
                    break;
                default:
                    throw new InvalidArgumentException(sprintf('Unknown operator: %s', $token));
            }
        }

        if ($parenthesisLevel !== 0) {
            throw new InvalidArgumentException('Mismatched parentheses.');
        }

        if (count($stack) !== 1) {
            throw new InvalidArgumentException('Invalid expression.');
        }

        return $stack[0];
    }

    /**
     * @param list<float> $stack
     *
     * @return array{0: float, 1: float}
     */
    private function popBinaryOperands(array &$stack): array
    {
        if (count($stack) < 2) {
            throw new InvalidArgumentException('Invalid expression.');
        }

        $right = array_pop($stack);
        $left = array_pop($stack);

        return [$left, $right];
    }

    /**
     * @param list<float> $stack
     */
    private function popUnaryOperand(array &$stack): float
    {
        if ($stack === []) {
            throw new InvalidArgumentException('Invalid expression.');
        }

        return array_pop($stack);
    }
}
