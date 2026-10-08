<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Filter\SelectFilter;
use Sylius\Component\Grid\Tests\Double\RecordingDataSource;

final class SelectFilterTest extends TestCase
{
    private SelectFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new SelectFilter();
    }

    public function testScalarAndArray(): void
    {
        $dataSource = new RecordingDataSource();
        $this->filter->apply($dataSource, 'select', '7', []);
        self::assertSame('equals', $dataSource->expressionBuilder->calls[0]['method']);

        $dataSource = new RecordingDataSource();
        $this->filter->apply($dataSource, 'select', ['4', '2'], []);
        self::assertSame('in', $dataSource->expressionBuilder->calls[0]['method']);
    }

    public function testZeroValueIsApplied(): void
    {
        $dataSource = new RecordingDataSource();
        $this->filter->apply($dataSource, 'select', '0', []);
        self::assertSame('equals', $dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame('0', $dataSource->expressionBuilder->calls[0]['args'][1]);

        $dataSource = new RecordingDataSource();
        $this->filter->apply($dataSource, 'select', 0, []);
        self::assertSame(0, $dataSource->expressionBuilder->calls[0]['args'][1]);

        $dataSource = new RecordingDataSource();
        $this->filter->apply($dataSource, 'select', ['0'], []);
        self::assertSame(['0'], $dataSource->expressionBuilder->calls[0]['args'][1]);
    }

    public function testEmptyStringDoesNothing(): void
    {
        $dataSource = new RecordingDataSource();
        $this->filter->apply($dataSource, 'select', '', []);

        self::assertCount(0, $dataSource->restrictions);
    }
}
