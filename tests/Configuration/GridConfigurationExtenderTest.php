<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Configuration\GridConfigurationExtender;

final class GridConfigurationExtenderTest extends TestCase
{
    private GridConfigurationExtender $extender;

    protected function setUp(): void
    {
        $this->extender = new GridConfigurationExtender();
    }

    public function testChildOverridesParentScalars(): void
    {
        $result = $this->extender->extends(
            ['foo' => 'fighters'],
            ['configuration1' => 'value1', 'foo' => 'bar']
        );

        self::assertSame([
            'configuration1' => 'value1',
            'foo' => 'fighters',
        ], $result);
    }

    public function testParentSortingIsNotInherited(): void
    {
        $result = $this->extender->extends(
            ['foo' => 'fighters'],
            ['sorting' => ['name' => 'asc']]
        );

        self::assertSame(['foo' => 'fighters'], $result);
    }

    public function testExtendsKeyRemoved(): void
    {
        $result = $this->extender->extends(['extends' => 'Artist'], []);

        self::assertSame([], $result);
    }

    public function testRecursiveFieldMerge(): void
    {
        $result = $this->extender->extends(
            [
                'fields' => [
                    'title' => [
                        'label' => 'Title',
                        'options' => ['foo' => 'bar'],
                    ],
                ],
            ],
            [
                'fields' => [
                    'title' => [
                        'type' => 'string',
                        'options' => ['template' => 'a.html.twig'],
                    ],
                ],
            ]
        );

        self::assertSame('Title', $result['fields']['title']['label']);
        self::assertSame('a.html.twig', $result['fields']['title']['options']['template']);
        self::assertSame('bar', $result['fields']['title']['options']['foo']);
    }
}
