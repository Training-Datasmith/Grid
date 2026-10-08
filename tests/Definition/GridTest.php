<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Definition;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\ActionGroup;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Tests\AssertThrows;

final class GridTest extends TestCase
{
    private Grid $grid;

    protected function setUp(): void
    {
        $this->grid = Grid::fromCodeAndDriverConfiguration('app_book', 'doctrine/orm', ['class' => 'App\\Book']);
    }

    public function testCodeDriverAndConfiguration(): void
    {
        self::assertSame('app_book', $this->grid->getCode());
        self::assertSame('doctrine/orm', $this->grid->getDriver());
        self::assertSame(['class' => 'App\\Book'], $this->grid->getDriverConfiguration());

        $this->grid->setDriverConfiguration(['foo' => 'bar']);
        self::assertSame(['foo' => 'bar'], $this->grid->getDriverConfiguration());
    }

    public function testSortingAndLimits(): void
    {
        self::assertSame([], $this->grid->getSorting());
        self::assertSame([], $this->grid->getLimits());

        $this->grid->setSorting(['name' => 'asc']);
        $this->grid->setLimits([20, 50]);

        self::assertSame(['name' => 'asc'], $this->grid->getSorting());
        self::assertSame([20, 50], $this->grid->getLimits());
    }

    public function testFieldLifecycle(): void
    {
        $field = Field::fromNameAndType('name', 'string');
        $replacement = Field::fromNameAndType('name', 'twig');

        $this->grid->addField($field);
        self::assertTrue($this->grid->hasField('name'));
        self::assertSame($field, $this->grid->getField('name'));

        $this->grid->setField($replacement);
        self::assertSame($replacement, $this->grid->getField('name'));

        $this->grid->removeField('name');
        self::assertFalse($this->grid->hasField('name'));

        $exception = AssertThrows::throwable(\InvalidArgumentException::class, function (): void {
            $this->grid->getField('nope');
        });
        self::assertStringContainsString('nope', $exception->getMessage());

        $this->grid->addField($field);
        AssertThrows::throwable(\InvalidArgumentException::class, function () use ($field): void {
            $this->grid->addField($field);
        });
    }

    public function testEnabledFields(): void
    {
        $enabled = Field::fromNameAndType('first', 'string');
        $disabled = Field::fromNameAndType('second', 'string');
        $disabled->setEnabled(false);

        $this->grid->addField($enabled);
        $this->grid->addField($disabled);

        $enabledFields = $this->grid->getEnabledFields();
        self::assertArrayHasKey('first', $enabledFields);
        self::assertArrayNotHasKey('second', $enabledFields);
    }

    public function testFiltersAndActions(): void
    {
        $filter = Filter::fromNameAndType('enabled', 'boolean');
        $this->grid->addFilter($filter);
        self::assertSame($filter, $this->grid->getFilter('enabled'));

        $actionGroup = ActionGroup::named('row');
        $action = Action::fromNameAndType('edit', 'link');
        $actionGroup->addAction($action);
        $this->grid->addActionGroup($actionGroup);

        self::assertSame(['edit' => $action], $this->grid->getActions('row'));

        $disabled = Action::fromNameAndType('delete', 'link');
        $disabled->setEnabled(false);
        $actionGroup->addAction($disabled);

        self::assertCount(1, $this->grid->getEnabledActions('row'));
    }

    public function testEnabledActionGroupsReturnsAllAdded(): void
    {
        $this->grid->addActionGroup(ActionGroup::named('a'));
        $this->grid->addActionGroup(ActionGroup::named('b'));

        self::assertCount(2, $this->grid->getEnabledActionGroups());
    }

    public function testDuplicateFilterThrows(): void
    {
        $filter = Filter::fromNameAndType('enabled', 'boolean');
        $this->grid->addFilter($filter);

        $exception = AssertThrows::throwable(\InvalidArgumentException::class, function () use ($filter): void {
            $this->grid->addFilter($filter);
        });

        self::assertStringContainsString('enabled', $exception->getMessage());
    }

    public function testMissingFilterThrows(): void
    {
        $exception = AssertThrows::throwable(\InvalidArgumentException::class, function (): void {
            $this->grid->getFilter('missing');
        });

        self::assertStringContainsString('missing', $exception->getMessage());
    }

    public function testDuplicateActionGroupThrows(): void
    {
        $this->grid->addActionGroup(ActionGroup::named('row'));

        $exception = AssertThrows::throwable(\InvalidArgumentException::class, function (): void {
            $this->grid->addActionGroup(ActionGroup::named('row'));
        });

        self::assertStringContainsString('row', $exception->getMessage());
    }

    public function testMissingActionGroupThrows(): void
    {
        $exception = AssertThrows::throwable(\InvalidArgumentException::class, function (): void {
            $this->grid->getActionGroup('missing');
        });

        self::assertStringContainsString('missing', $exception->getMessage());
    }
}
