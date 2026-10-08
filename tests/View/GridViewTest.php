<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\View;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Tests\AssertThrows;
use Sylius\Component\Grid\View\GridView;

final class GridViewTest extends TestCase
{
    public function testAccessors(): void
    {
        $grid = Grid::fromCodeAndDriverConfiguration('app', 'array', []);
        $parameters = new Parameters(['page' => 1]);
        $view = new GridView(['row'], $grid, $parameters);

        self::assertSame(['row'], $view->getData());
        self::assertSame($grid, $view->getDefinition());
        self::assertSame($parameters, $view->getParameters());
    }

    public function testDefaultSingleSortField(): void
    {
        $grid = $this->gridWithSortableFields();
        $grid->setSorting(['name' => 'asc']);
        $view = new GridView([], $grid, new Parameters());

        self::assertTrue($view->isSortedBy('name'));
        self::assertFalse($view->isSortedBy('code'));
        self::assertSame('asc', $view->getSortingOrder('name'));
    }

    public function testParameterSortingOverridesActiveField(): void
    {
        $grid = $this->gridWithSortableFields();
        $grid->setSorting(['name' => 'asc']);
        $view = new GridView([], $grid, new Parameters(['sorting' => ['name' => 'desc']]));

        self::assertTrue($view->isSortedBy('name'));
        self::assertSame('desc', $view->getSortingOrder('name'));
    }

    public function testMergedSortingOrderForInactiveSortableField(): void
    {
        $grid = $this->gridWithSortableFields();
        $grid->setSorting(['name' => 'asc']);
        $view = new GridView([], $grid, new Parameters(['sorting' => ['enabled' => 'desc']]));

        self::assertSame('asc', $view->getSortingOrder('code'));
    }

    public function testMissingOrNonSortableFieldThrows(): void
    {
        $grid = $this->gridWithSortableFields();
        $view = new GridView([], $grid, new Parameters());

        AssertThrows::throwable(\InvalidArgumentException::class, function () use ($view): void {
            $view->getSortingOrder('missing');
        });

        $grid->addField(Field::fromNameAndType('plain', 'string'));
        $view = new GridView([], $grid, new Parameters());

        AssertThrows::throwable(\InvalidArgumentException::class, function () use ($view): void {
            $view->isSortedBy('plain');
        });
    }

    private function gridWithSortableFields(): Grid
    {
        $grid = Grid::fromCodeAndDriverConfiguration('app', 'array', []);

        $name = Field::fromNameAndType('name', 'string');
        $name->setSortable('name');
        $grid->addField($name);

        $code = Field::fromNameAndType('code', 'string');
        $code->setSortable('code');
        $grid->addField($code);

        $enabled = Field::fromNameAndType('enabled', 'boolean');
        $enabled->setSortable('enabled');
        $grid->addField($enabled);

        return $grid;
    }
}
