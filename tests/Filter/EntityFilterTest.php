<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Filter\EntityFilter;
use Sylius\Component\Grid\Tests\AssertRestrictions;
use Sylius\Component\Grid\Tests\Double\RecordingDataSource;

final class EntityFilterTest extends TestCase
{
    public function testScalarUsesFilterNameAsField(): void
    {
        $filter = new EntityFilter();
        $dataSource = new RecordingDataSource();

        $filter->apply($dataSource, 'entity', '7', []);

        AssertRestrictions::assertAt($dataSource, 0, AssertRestrictions::tokenAt($dataSource, 1));
        self::assertSame('equals', $dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame(['entity', '7'], $dataSource->expressionBuilder->calls[0]['args']);
        self::assertSame('orX', $dataSource->expressionBuilder->calls[1]['method']);
    }

    public function testMultipleValuesOrCombined(): void
    {
        $filter = new EntityFilter();
        $dataSource = new RecordingDataSource();

        $filter->apply($dataSource, 'entity', ['4', '2'], []);

        self::assertCount(3, $dataSource->expressionBuilder->calls);
        AssertRestrictions::assertAt($dataSource, 0, AssertRestrictions::tokenAt($dataSource, 2));
        self::assertSame('orX', $dataSource->expressionBuilder->calls[2]['method']);
    }

    public function testCustomFieldsOption(): void
    {
        $filter = new EntityFilter();
        $dataSource = new RecordingDataSource();

        $filter->apply($dataSource, 'entity', '3', ['fields' => ['owner', 'assignee']]);

        self::assertSame(['owner', '3'], $dataSource->expressionBuilder->calls[0]['args']);
        self::assertSame(['assignee', '3'], $dataSource->expressionBuilder->calls[1]['args']);
        AssertRestrictions::assertAt($dataSource, 0, AssertRestrictions::tokenAt($dataSource, 2));
    }

    public function testEmptyDoesNothing(): void
    {
        $filter = new EntityFilter();
        $dataSource = new RecordingDataSource();
        $filter->apply($dataSource, 'entity', '', []);

        self::assertCount(0, $dataSource->restrictions);
    }
}
