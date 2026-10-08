<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Filter\EntityFilter;
use Sylius\Component\Grid\Tests\Double\RecordingDataSource;

final class EntityFilterTest extends TestCase
{
    public function testScalarAndMultipleValues(): void
    {
        $filter = new EntityFilter();
        $dataSource = new RecordingDataSource();

        $filter->apply($dataSource, 'entity', '7', []);
        self::assertSame('equals', $dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame('orX', $dataSource->expressionBuilder->calls[1]['method']);

        $dataSource = new RecordingDataSource();
        $filter->apply($dataSource, 'entity', ['4', '2'], []);
        self::assertCount(3, $dataSource->expressionBuilder->calls);
        self::assertSame('orX', $dataSource->expressionBuilder->calls[2]['method']);
    }

    public function testEmptyDoesNothing(): void
    {
        $filter = new EntityFilter();
        $dataSource = new RecordingDataSource();
        $filter->apply($dataSource, 'entity', '', []);

        self::assertCount(0, $dataSource->restrictions);
    }
}
