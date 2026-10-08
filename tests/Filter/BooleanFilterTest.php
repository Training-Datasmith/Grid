<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Filter\BooleanFilter;
use Sylius\Component\Grid\Tests\AssertRestrictions;
use Sylius\Component\Grid\Tests\Double\RecordingDataSource;

final class BooleanFilterTest extends TestCase
{
    public function testTrueAndFalseStrings(): void
    {
        $filter = new BooleanFilter();
        $dataSource = new RecordingDataSource();

        $filter->apply($dataSource, 'enabled', BooleanFilter::TRUE, []);
        AssertRestrictions::assertAt($dataSource, 0, AssertRestrictions::tokenAt($dataSource, 0));
        self::assertTrue($dataSource->expressionBuilder->calls[0]['args'][1]);

        $dataSource = new RecordingDataSource();
        $filter->apply($dataSource, 'enabled', BooleanFilter::FALSE, []);
        AssertRestrictions::assertAt($dataSource, 0, AssertRestrictions::tokenAt($dataSource, 0));
        self::assertFalse($dataSource->expressionBuilder->calls[0]['args'][1]);
    }

    public function testFieldOptionOverridesName(): void
    {
        $filter = new BooleanFilter();
        $dataSource = new RecordingDataSource();

        $filter->apply($dataSource, 'enabled', BooleanFilter::TRUE, ['field' => 'isActive']);

        AssertRestrictions::assertAt($dataSource, 0, AssertRestrictions::tokenAt($dataSource, 0));
        self::assertSame(['isActive', true], $dataSource->expressionBuilder->calls[0]['args']);
    }

    public function testEmptyDoesNothing(): void
    {
        $filter = new BooleanFilter();
        $dataSource = new RecordingDataSource();
        $filter->apply($dataSource, 'enabled', '', []);

        self::assertCount(0, $dataSource->restrictions);
    }
}
