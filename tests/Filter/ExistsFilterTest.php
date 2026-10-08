<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Filter\ExistsFilter;
use Sylius\Component\Grid\Tests\AssertRestrictions;
use Sylius\Component\Grid\Tests\Double\RecordingDataSource;

final class ExistsFilterTest extends TestCase
{
    public function testNullDoesNothing(): void
    {
        $filter = new ExistsFilter();
        $dataSource = new RecordingDataSource();
        $filter->apply($dataSource, 'filterName', null, []);

        self::assertCount(0, $dataSource->restrictions);
    }

    public function testTrueAndFalse(): void
    {
        $filter = new ExistsFilter();
        $dataSource = new RecordingDataSource();
        $filter->apply($dataSource, 'filterName', ExistsFilter::TRUE, ['field' => 'fieldName']);
        AssertRestrictions::assertAt($dataSource, 0, AssertRestrictions::tokenAt($dataSource, 0));
        self::assertSame('isNotNull', $dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame(['fieldName'], $dataSource->expressionBuilder->calls[0]['args']);

        $dataSource = new RecordingDataSource();
        $filter->apply($dataSource, 'filterName', ExistsFilter::FALSE, ['field' => 'fieldName']);
        AssertRestrictions::assertAt($dataSource, 0, AssertRestrictions::tokenAt($dataSource, 0));
        self::assertSame('isNull', $dataSource->expressionBuilder->calls[0]['method']);
    }
}
