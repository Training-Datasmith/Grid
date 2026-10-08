<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Data;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Data\DataProvider;
use Sylius\Component\Grid\Data\DataSourceProvider;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filter\StringFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Sylius\Component\Grid\Filtering\FiltersApplicator;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolver;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Sorting\Sorter;
use Sylius\Component\Grid\Tests\Double\FixedDriver;
use Sylius\Component\Grid\Tests\Double\RecordingDataSource;
use Sylius\Component\Registry\ServiceRegistry;

final class DataProviderTest extends TestCase
{
    public function testAppliesFiltersThenSortsThenReturnsData(): void
    {
        $dataSource = new RecordingDataSource(['row']);
        $driverRegistry = new ServiceRegistry(DriverInterface::class, 'driver');
        $driverRegistry->register('array', new FixedDriver($dataSource));

        $filterRegistry = new ServiceRegistry(FilterInterface::class, 'filter');
        $filterRegistry->register('string', new StringFilter());

        $dataProvider = new DataProvider(
            new DataSourceProvider($driverRegistry),
            new FiltersApplicator($filterRegistry, new FiltersCriteriaResolver()),
            new Sorter()
        );

        $grid = Grid::fromCodeAndDriverConfiguration('app', 'array', []);
        $name = Field::fromNameAndType('name', 'string');
        $name->setSortable('name');
        $grid->addField($name);
        $grid->addFilter(Filter::fromNameAndType('name', 'string'));

        $parameters = new Parameters([
            'criteria' => ['name' => 'Ada'],
            'sorting' => ['name' => 'asc'],
        ]);

        self::assertSame(['row'], $dataProvider->getData($grid, $parameters));

        $methods = array_column($dataSource->expressionBuilder->calls, 'method');
        self::assertSame('like', $methods[0]);
        self::assertSame('addOrderBy', $methods[1]);
        self::assertSame(['name', 'asc'], $dataSource->expressionBuilder->calls[1]['args']);
    }
}
