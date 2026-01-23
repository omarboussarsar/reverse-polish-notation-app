<?php

declare(strict_types=1);

namespace App\Service;

use InvalidArgumentException;

final class ReversePolishNotation
{
    public function evaluate(string $expression): float
    {
        $expression = trim($expression);
        if ($expression == '') {
            throw new InvalidArgumentException('Expression cannot be empty.');
        }

        $stack = [];
        $tokens = preg_split('/\s+/', $expression) ?: [];

        foreach ($tokens as $token) {
            if (is_numeric($token)) {
                if ($token >= PHP_INT_MAX) {
                    throw new InvalidArgumentException('Integer overflow.');
                }
                
                $stack[] = (float) $token;
                continue;
            } else {
                $right = array_pop($stack);
                $left = array_pop($stack);

                if ('+' === $token) {
                    $result = $left + $right;
                    $stack[] = $result;
                } elseif ('-' === $token) {
                    $result = $left - $right;
                    $stack[] = $result;
                } elseif ('*' === $token) {
                    $result = $left * $right;
                    $stack[] = $result;
                } elseif ('/' === $token) {
                    if (0 == $right) {
                        throw new InvalidArgumentException('Division by zero.');
                    }
                    $result = $left / $right;
                    $stack[] = $result;
                } elseif ('^' === $token) {
                    $result = $left ** $right;
                    $stack[] = $result;
                } elseif (('!' === $token)) {
                    $factorial = 1;
                    for ($i = 1; $i <= (int)$right; $i++) {
                        $factorial *= $i;
                    }
                    $stack[] = (float)$factorial;
                } elseif ('mod' === $token) {
                    if (0 == $right) {
                        throw new InvalidArgumentException('Division by zero.');
                    }
                    $result = $left % $right;
                    $stack[] = $result;
                } else {
                    throw new InvalidArgumentException(sprintf('Unknown operator: %s', $token));
                }
            }
        }

        if (count($stack) !== 1) {
            throw new InvalidArgumentException('Invalid expression.');
        }

        return $stack[0];
    }
}
