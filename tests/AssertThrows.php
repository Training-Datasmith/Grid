<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests;

use PHPUnit\Framework\Assert;

final class AssertThrows
{
    /**
     * @template T of \Throwable
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public static function throwable(string $class, callable $callback): \Throwable
    {
        try {
            $callback();
        } catch (\Throwable $exception) {
            Assert::assertInstanceOf($class, $exception);

            return $exception;
        }

        Assert::fail(sprintf('Expected %s to be thrown.', $class));
    }
}
