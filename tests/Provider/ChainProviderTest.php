<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\ChainProvider;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Grid\Tests\AssertThrows;

final class ChainProviderTest extends TestCase
{
    public function testReturnsFromSecondProvider(): void
    {
        $grid = Grid::fromCodeAndDriverConfiguration('app', 'array', []);
        $first = new ThrowingProvider();
        $second = new FixedProvider($grid);

        $chain = new ChainProvider([$first, $second]);

        self::assertSame($grid, $chain->get('app_book'));
    }

    public function testStopsAtFirstProviderThatReturnsGrid(): void
    {
        $grid = Grid::fromCodeAndDriverConfiguration('app', 'array', []);
        $first = new FixedProvider($grid);
        $second = new CountingProvider();
        $chain = new ChainProvider([$first, $second]);

        self::assertSame($grid, $chain->get('app_book'));
        self::assertSame(0, $second->calls);
    }

    public function testNonUndefinedExceptionPropagates(): void
    {
        $chain = new ChainProvider([new RuntimeThrowingProvider()]);

        $exception = AssertThrows::throwable(\RuntimeException::class, function () use ($chain): void {
            $chain->get('app');
        });

        self::assertStringContainsString('boom', $exception->getMessage());
    }

    public function testAllProvidersTriedThenUndefinedGridException(): void
    {
        $first = new CountingProvider();
        $second = new CountingProvider();
        $chain = new ChainProvider([$first, $second]);

        AssertThrows::throwable(UndefinedGridException::class, function () use ($chain): void {
            $chain->get('app_book');
        });

        self::assertSame(1, $first->calls);
        self::assertSame(1, $second->calls);
    }
}

final class ThrowingProvider implements GridProviderInterface
{
    public function get(string $code): Grid
    {
        throw new UndefinedGridException($code);
    }
}

final class FixedProvider implements GridProviderInterface
{
    public function __construct(private Grid $grid)
    {
    }

    public function get(string $code): Grid
    {
        return $this->grid;
    }
}

final class RuntimeThrowingProvider implements GridProviderInterface
{
    public function get(string $code): Grid
    {
        throw new \RuntimeException('boom');
    }
}

final class CountingProvider implements GridProviderInterface
{
    public int $calls = 0;

    public function get(string $code): Grid
    {
        ++$this->calls;

        throw new UndefinedGridException($code);
    }
}
