<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Sorting;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Sorting\Sorter;
use Sylius\Component\Grid\Tests\AssertThrows;
use Sylius\Component\Grid\Tests\Double\RecordingDataSource;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class SorterTest extends TestCase
{
    private Sorter $sorter;

    protected function setUp(): void
    {
        $this->sorter = new Sorter();
    }

    public function testSortsUsingGridDefault(): void
    {
        $dataSource = new RecordingDataSource();
        $grid = $this->gridWithSortableName();
        $grid->setSorting(['name' => 'desc']);

        $this->sorter->sort($dataSource, $grid, new Parameters());

        self::assertSame('addOrderBy', $dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame(['translation.name', 'desc'], $dataSource->expressionBuilder->calls[0]['args']);
    }

    public function testParameterSortingOverridesDefault(): void
    {
        $dataSource = new RecordingDataSource();
        $grid = $this->gridWithSortableName();
        $grid->setSorting(['code' => 'desc']);

        $this->sorter->sort($dataSource, $grid, new Parameters(['sorting' => ['name' => 'asc']]));

        self::assertCount(1, $dataSource->expressionBuilder->calls);
        self::assertSame(['translation.name', 'asc'], $dataSource->expressionBuilder->calls[0]['args']);
    }

    public function testEmptyParameterSortingAppliesNoOrder(): void
    {
        $dataSource = new RecordingDataSource();
        $grid = $this->gridWithSortableName();
        $grid->setSorting(['name' => 'desc']);

        $this->sorter->sort($dataSource, $grid, new Parameters(['sorting' => []]));

        self::assertCount(0, $dataSource->expressionBuilder->calls);
    }

    public function testInvalidDirectionThrowsBeforeOrdering(): void
    {
        $dataSource = new RecordingDataSource();
        $grid = $this->gridWithSortableName();

        AssertThrows::throwable(BadRequestHttpException::class, function () use ($dataSource, $grid): void {
            $this->sorter->sort($dataSource, $grid, new Parameters(['sorting' => ['name' => 'sideways']]));
        });

        self::assertCount(0, $dataSource->expressionBuilder->calls);
    }

    public function testUnknownFieldThrowsBeforeOrdering(): void
    {
        $dataSource = new RecordingDataSource();
        $grid = $this->gridWithSortableName();

        $exception = AssertThrows::throwable(BadRequestHttpException::class, function () use ($dataSource, $grid): void {
            $this->sorter->sort($dataSource, $grid, new Parameters(['sorting' => ['missing' => 'asc']]));
        });

        self::assertStringContainsString('missing', $exception->getMessage());
        self::assertCount(0, $dataSource->expressionBuilder->calls);
    }

    private function gridWithSortableName(): Grid
    {
        $grid = Grid::fromCodeAndDriverConfiguration('app', 'array', []);
        $name = Field::fromNameAndType('name', 'string');
        $name->setSortable('translation.name');
        $grid->addField($name);
        $code = Field::fromNameAndType('code', 'string');
        $code->setSortable('translation.code');
        $grid->addField($code);

        return $grid;
    }
}
