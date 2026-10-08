<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Filter\DateFilter;
use Sylius\Component\Grid\Tests\Double\RecordingDataSource;

final class DateFilterTest extends TestCase
{
    private DateFilter $filter;

    private RecordingDataSource $dataSource;

    protected function setUp(): void
    {
        $this->filter = new DateFilter();
        $this->dataSource = new RecordingDataSource();
    }

    public function testFromInclusiveDefault(): void
    {
        $this->filter->apply($this->dataSource, 'checkoutCompletedAt', [
            'from' => ['date' => '2016-12-05', 'time' => '08:00'],
        ], []);

        self::assertSame('greaterThanOrEqual', $this->dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame(['checkoutCompletedAt', '2016-12-05 08:00'], $this->dataSource->expressionBuilder->calls[0]['args']);
    }

    public function testToExclusiveDefault(): void
    {
        $this->filter->apply($this->dataSource, 'checkoutCompletedAt', [
            'to' => ['date' => '2016-12-06', 'time' => '08:00'],
        ], []);

        self::assertSame('lessThan', $this->dataSource->expressionBuilder->calls[0]['method']);
    }

    public function testEmptyFromDateDoesNotRestrict(): void
    {
        $this->filter->apply($this->dataSource, 'checkoutCompletedAt', [
            'from' => ['date' => '', 'time' => ''],
        ], []);

        self::assertCount(0, $this->dataSource->restrictions);
    }

    public function testCustomFieldOption(): void
    {
        $this->filter->apply($this->dataSource, 'filterName', [
            'to' => ['date' => '2016-12-06', 'time' => ''],
        ], ['field' => 'completedAt']);

        self::assertSame('completedAt', $this->dataSource->expressionBuilder->calls[0]['args'][0]);
    }
}
