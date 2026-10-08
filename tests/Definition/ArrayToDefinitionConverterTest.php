<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Definition;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverter;
use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class ArrayToDefinitionConverterTest extends TestCase
{
    private ArrayToDefinitionConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new ArrayToDefinitionConverter(new EventDispatcher());
    }

    public function testConvertsFullConfiguration(): void
    {
        $configuration = [
            'driver' => ['name' => 'doctrine/orm', 'options' => ['resource' => 'sylius.tax_category']],
            'sorting' => ['code' => 'desc'],
            'limits' => [9, 18],
            'fields' => [
                'code' => [
                    'type' => 'string',
                    'label' => 'System Code',
                    'path' => 'method.code',
                    'sortable' => 'code',
                    'options' => ['template' => 'bar.html.twig'],
                ],
            ],
            'filters' => [
                'enabled' => [
                    'type' => 'boolean',
                    'default_value' => 'true',
                ],
            ],
            'actions' => [
                'default' => [
                    'view' => ['type' => 'link', 'label' => 'Display'],
                ],
            ],
        ];

        $grid = $this->converter->convert('sylius_admin_tax_category', $configuration);

        self::assertSame('sylius_admin_tax_category', $grid->getCode());
        self::assertSame(['code' => 'desc'], $grid->getSorting());
        self::assertSame([9, 18], $grid->getLimits());
        self::assertSame('System Code', $grid->getField('code')->getLabel());
        self::assertSame('true', $grid->getFilter('enabled')->getCriteria());
    }

    public function testSortableTrueNullAndFalse(): void
    {
        $configuration = [
            'driver' => ['name' => 'doctrine/orm', 'options' => []],
            'fields' => [
                'a' => ['type' => 'string', 'sortable' => true],
                'b' => ['type' => 'string', 'sortable' => null],
                'c' => ['type' => 'string', 'sortable' => false],
                'd' => ['type' => 'string'],
            ],
        ];

        $grid = $this->converter->convert('app', $configuration);

        self::assertSame('a', $grid->getField('a')->getSortable());
        self::assertSame('b', $grid->getField('b')->getSortable());
        self::assertNull($grid->getField('c')->getSortable());
        self::assertFalse($grid->getField('c')->isSortable());
        self::assertFalse($grid->getField('d')->isSortable());
    }

    public function testEventNameAndMutation(): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener('sylius.grid.app_book', function (GridDefinitionConverterEvent $event): void {
            $event->getGrid()->getField('name')->setEnabled(false);
        });

        $converter = new ArrayToDefinitionConverter($dispatcher);
        $grid = $converter->convert('app_book', [
            'driver' => ['name' => 'array', 'options' => []],
            'fields' => ['name' => ['type' => 'string']],
        ]);

        self::assertFalse($grid->getField('name')->isEnabled());
    }

    public function testOmittedSortingAndLimitsStayEmpty(): void
    {
        $grid = $this->converter->convert('app', [
            'driver' => ['name' => 'array', 'options' => []],
        ]);

        self::assertSame([], $grid->getSorting());
        self::assertSame([], $grid->getLimits());
    }
}
