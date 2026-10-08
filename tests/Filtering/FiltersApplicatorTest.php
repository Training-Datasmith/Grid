<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Filtering;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filter\StringFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Sylius\Component\Grid\Filtering\FiltersApplicator;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolver;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Tests\AssertThrows;
use Sylius\Component\Grid\Tests\Double\RecordingDataSource;
use Sylius\Component\Registry\ServiceRegistry;

final class FiltersApplicatorTest extends TestCase
{
    public function testNoCriteriaDoesNothing(): void
    {
        $dataSource = new RecordingDataSource();
        $applicator = $this->applicator();

        $grid = Grid::fromCodeAndDriverConfiguration('app', 'array', []);
        $grid->addFilter(Filter::fromNameAndType('keywords', 'string'));

        $applicator->apply($dataSource, $grid, new Parameters());

        self::assertSame([], $dataSource->restrictions);
    }

    public function testAppliesKnownFilterSkipsUnknownName(): void
    {
        $dataSource = new RecordingDataSource();
        $applicator = $this->applicator();

        $grid = Grid::fromCodeAndDriverConfiguration('app', 'array', []);
        $grid->addFilter(Filter::fromNameAndType('keywords', 'string'));

        $applicator->apply($dataSource, $grid, new Parameters([
            'criteria' => ['keywords' => 'Banana', 'enabled' => true],
        ]));

        self::assertCount(1, $dataSource->restrictions);
        self::assertSame('like', $dataSource->expressionBuilder->calls[0]['method']);
    }

    public function testMissingFilterTypePropagates(): void
    {
        $dataSource = new RecordingDataSource();
        $registry = new ServiceRegistry(FilterInterface::class, 'filter');
        $applicator = new FiltersApplicator($registry, new FiltersCriteriaResolver());

        $grid = Grid::fromCodeAndDriverConfiguration('app', 'array', []);
        $grid->addFilter(Filter::fromNameAndType('keywords', 'missing'));

        AssertThrows::throwable(\InvalidArgumentException::class, function () use ($applicator, $dataSource, $grid): void {
            $applicator->apply($dataSource, $grid, new Parameters(['criteria' => ['keywords' => 'x']]));
        });

        self::assertSame([], $dataSource->restrictions);
    }

    private function applicator(): FiltersApplicator
    {
        $registry = new ServiceRegistry(FilterInterface::class, 'filter');
        $registry->register('string', new StringFilter());

        return new FiltersApplicator($registry, new FiltersCriteriaResolver());
    }
}
