<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Filter\MoneyFilter;
use Sylius\Component\Grid\Tests\AssertRestrictions;
use Sylius\Component\Grid\Tests\Double\RecordingDataSource;

final class MoneyFilterTest extends TestCase
{
    public function testGreaterThanNormalizedAmount(): void
    {
        $filter = new MoneyFilter();
        $dataSource = new RecordingDataSource();

        $filter->apply($dataSource, 'total', [
            'greaterThan' => '12.00',
            'lessThan' => '',
            'currency' => '',
        ], ['currency_field' => 'currencyCode']);

        AssertRestrictions::assertAt($dataSource, 0, AssertRestrictions::tokenAt($dataSource, 0));
        self::assertSame('greaterThan', $dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame(1200, $dataSource->expressionBuilder->calls[0]['args'][1]);
    }

    public function testLessThanNormalizedAmount(): void
    {
        $filter = new MoneyFilter();
        $dataSource = new RecordingDataSource();

        $filter->apply($dataSource, 'total', [
            'greaterThan' => '',
            'lessThan' => '9.50',
            'currency' => '',
        ], ['currency_field' => 'currencyCode']);

        AssertRestrictions::assertAt($dataSource, 0, AssertRestrictions::tokenAt($dataSource, 0));
        self::assertSame('lessThan', $dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame(950, $dataSource->expressionBuilder->calls[0]['args'][1]);
    }

    public function testCurrencyRestrictionUsesCurrencyField(): void
    {
        $filter = new MoneyFilter();
        $dataSource = new RecordingDataSource();

        $filter->apply($dataSource, 'total', [
            'greaterThan' => '',
            'lessThan' => '',
            'currency' => 'USD',
        ], ['currency_field' => 'currencyCode']);

        AssertRestrictions::assertCount($dataSource, 1);
        AssertRestrictions::assertAt($dataSource, 0, AssertRestrictions::tokenAt($dataSource, 0));
        self::assertSame('equals', $dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame(['currencyCode', 'USD'], $dataSource->expressionBuilder->calls[0]['args']);
    }

    public function testCustomScale(): void
    {
        $filter = new MoneyFilter();
        $dataSource = new RecordingDataSource();

        $filter->apply($dataSource, 'total', [
            'greaterThan' => '1.234',
            'lessThan' => '',
            'currency' => '',
        ], ['currency_field' => 'currencyCode', 'scale' => 3]);

        AssertRestrictions::assertAt($dataSource, 0, AssertRestrictions::tokenAt($dataSource, 0));
        self::assertSame(1234, $dataSource->expressionBuilder->calls[0]['args'][1]);
    }

    public function testEmptyDataDoesNothing(): void
    {
        $filter = new MoneyFilter();
        $dataSource = new RecordingDataSource();
        $filter->apply($dataSource, 'total', [], ['currency_field' => 'currencyCode']);

        self::assertCount(0, $dataSource->restrictions);
    }
}
