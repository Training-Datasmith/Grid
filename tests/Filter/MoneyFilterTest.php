<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Filter\MoneyFilter;
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

        self::assertSame('greaterThan', $dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame(1200, $dataSource->expressionBuilder->calls[0]['args'][1]);
    }

    public function testEmptyDataDoesNothing(): void
    {
        $filter = new MoneyFilter();
        $dataSource = new RecordingDataSource();
        $filter->apply($dataSource, 'total', [], ['currency_field' => 'currencyCode']);

        self::assertCount(0, $dataSource->restrictions);
    }
}
