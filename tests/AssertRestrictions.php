<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests;

use PHPUnit\Framework\Assert;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Tests\Double\ExpressionToken;
use Sylius\Component\Grid\Tests\Double\RecordingDataSource;

final class AssertRestrictions
{
    public static function assertCount(RecordingDataSource $dataSource, int $expected): void
    {
        Assert::assertCount($expected, $dataSource->restrictions);
    }

    public static function assertAt(
        RecordingDataSource $dataSource,
        int $index,
        ExpressionToken $expression,
        string $condition = DataSourceInterface::CONDITION_AND,
    ): void {
        Assert::assertArrayHasKey($index, $dataSource->restrictions);
        Assert::assertSame($expression, $dataSource->restrictions[$index]['expression']);
        Assert::assertSame($condition, $dataSource->restrictions[$index]['condition']);
    }

    public static function tokenAt(RecordingDataSource $dataSource, int $callIndex): ExpressionToken
    {
        $token = $dataSource->expressionBuilder->calls[$callIndex]['token'] ?? null;
        Assert::assertInstanceOf(ExpressionToken::class, $token);

        return $token;
    }
}
